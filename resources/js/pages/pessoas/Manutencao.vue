<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Button from '@/components/ui/button/Button.vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import InputError from '@/components/InputError.vue';
import Select from '@/components/ui/select/Select.vue';
import { LoaderCircle } from 'lucide-vue-next';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Bug, LaptopMinimalCheck, MessageCircleWarning } from 'lucide-vue-next';
import { watch } from 'vue';

interface Props {
    pessoa?: {
        id?: number;
        nome: string;
        cpf: string;
        tipo: string;
        telefone: string;
        email: string;
    };
}
const props = defineProps<Props>();
const title = props.pessoa ? 'Editar Pessoa' : 'Cadastrar Pessoa';

const validarCPF = (cpf: string): boolean => {
    cpf = cpf.replace(/\D/g, '');
    
    if (cpf.length !== 11) return false;
    
    // Verifica se todos os dígitos são iguais
    if (/^(\d)\1+$/.test(cpf)) return false;
    
    // Validação do primeiro dígito verificador
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = 11 - (soma % 11);
    let digitoVerificador1 = resto >= 10 ? 0 : resto;
    
    if (digitoVerificador1 !== parseInt(cpf.charAt(9))) return false;
    
    // Validação do segundo dígito verificador
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = 11 - (soma % 11);
    let digitoVerificador2 = resto >= 10 ? 0 : resto;
    
    return digitoVerificador2 === parseInt(cpf.charAt(10));
};


const validarCNPJ = (cnpj: string): boolean => {
    cnpj = cnpj.replace(/\D/g, '');
    
    if (cnpj.length !== 14) return false;
    
    // Verifica se todos os dígitos são iguais
    if (/^(\d)\1+$/.test(cnpj)) return false;
    
    // Validação do primeiro dígito verificador
    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    const digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
        if (pos < 2) pos = 9;
    }
    
    let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
    if (resultado !== parseInt(digitos.charAt(0))) return false;
    
    // Validação do segundo dígito verificador
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
        if (pos < 2) pos = 9;
    }
    
    resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
    return resultado === parseInt(digitos.charAt(1));
};

const validarDocumento = (documento: string, tipo: string): boolean => {
    const documentoLimpo = documento.replace(/\D/g, '');
    
    if (tipo === 'Física') {
        return validarCPF(documentoLimpo);
    } else {
        return validarCNPJ(documentoLimpo);
    }
};

const aplicarMascaraCPF = (cpf: string) => {
    cpf = cpf.replace(/\D/g, '');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return cpf;
};

const aplicarMascaraCNPJ = (cnpj: string) => {
    cnpj = cnpj.replace(/\D/g, '');
    cnpj = cnpj.replace(/^(\d{2})(\d)/, '$1.$2');
    cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    cnpj = cnpj.replace(/\.(\d{3})(\d)/, '.$1/$2');
    cnpj = cnpj.replace(/(\d{4})(\d)/, '$1-$2');
    return cnpj;
};

const aplicarMascara = (valor: string, tipo: string) => {
    if (!valor) return '';
    
    const apenasNumeros = valor.replace(/\D/g, '');
    
    if (tipo === 'Física') {
        return aplicarMascaraCPF(apenasNumeros);
    } else {
        return aplicarMascaraCNPJ(apenasNumeros);
    }
};

const aplicarMascaraTelefone = (telefone: string) => {
    telefone = telefone.replace(/\D/g, '');
    
    if (telefone.length === 11) {
        // Formato para celular: (00) 00000-0000
        telefone = telefone.replace(/(\d{2})(\d)/, '($1) $2');
        telefone = telefone.replace(/(\d{5})(\d{4})/, '$1-$2');
    } else if (telefone.length === 10) {
        // Formato para fixo: (00) 0000-0000
        telefone = telefone.replace(/(\d{2})(\d)/, '($1) $2');
        telefone = telefone.replace(/(\d{4})(\d{4})/, '$1-$2');
    }
    // Para outros casos (como números incompletos), não aplica máscara
    return telefone;
};

const handleTelefoneInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const valor = target.value;
    form.telefone = aplicarMascaraTelefone(valor);
};

const form = useForm({
    id: props.pessoa?.id || '',
    nome: props.pessoa?.nome || '',
    cpf: props.pessoa?.cpf || '',
    tipo: props.pessoa?.tipo || 'Física',
    telefone: props.pessoa?.telefone || '',
    email: props.pessoa?.email || '',
});

watch(() => form.tipo, (novoTipo, antigoValor) => {
    form.cpf = aplicarMascara(form.cpf, novoTipo);
});

const handleCpfInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const valor = target.value;
    form.cpf = aplicarMascara(valor, form.tipo);
};

const handleSubmit = () => {

    // Remove máscara para validação
    const documentoLimpo = form.cpf.replace(/\D/g, '');
    
    // Valida o documento baseado no tipo
    if (!validarDocumento(documentoLimpo, form.tipo)) {
        form.setError('cpf', form.tipo === 'Física' ? 'CPF inválido' : 'CNPJ inválido');
        return;
    }

    if (form.id) {
        form.put(`/pessoas/edit/${props.pessoa?.id}`);
    } else {
        form.post(`/pessoas`);
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: title,
        href: '/pessoas/manutencao',
    },
];

const page = usePage();
</script>

<template>

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="p-4">
            <div v-if="page.props.flash?.message" class="mb-4">
                <Alert>
                    <MessageCircleWarning class="h-4 w-4" />
                    <AlertTitle>Notificação:</AlertTitle>
                        <AlertDescription>
                            {{ page.props.flash.message }}
                        </AlertDescription>
                </Alert>
            </div>
            <div v-if="page.props.flash?.success" class="mb-4">
                <Alert>
                    <LaptopMinimalCheck class="h-4 w-4" />
                    <AlertTitle>Sucesso:</AlertTitle>
                        <AlertDescription>
                            {{ page.props.flash.success }}
                        </AlertDescription>
                </Alert>
            </div>
            <div v-if="page.props.flash?.error" class="mb-4">
                <Alert>
                    <Bug class="h-4 w-4" />
                    <AlertTitle>Erro: </AlertTitle>
                        <AlertDescription>
                            {{ page.props.flash.error }}
                        </AlertDescription>
                </Alert>
            </div>
        </div>

        <div class="p-4">
            <form class="w-8/12 space-y-4" @submit.prevent="handleSubmit">

                <!-- Campo ID (visível apenas em edição) -->
                <div v-if="form.id" class="grid gap-2">
                    <Label for="id">ID</Label>
                    <Input
                        id="id"
                        type="text"
                        v-model="form.id"
                        disabled
                    />
                    <InputError :message="form.errors.id" />
                </div>

                <!-- Campo Nome -->
                <div class="grid gap-2">
                    <Label for="nome">Nome Completo</Label>
                    <Input
                        id="nome"
                        type="text"
                        v-model="form.nome"
                        required
                        autofocus
                        :tabindex="1"
                        placeholder="Digite o nome completo"
                    />
                    <InputError :message="form.errors.nome" />
                </div>
                
                <!-- Campo Tipo (Pessoa Física/Jurídica) -->
                <div class="grid gap-2">
                    <Label for="tipo">Tipo de Pessoa</Label>
                    <Select
                        id="tipo"
                        v-model="form.tipo"
                        :tabindex="2"
                        placeholder="Selecione o tipo"
                    >
                        <option value="Física">Pessoa Física</option>
                        <option value="Jurídica">Pessoa Jurídica</option>
                    </Select>
                    <InputError :message="form.errors.tipo" />
                </div>

                <!-- Campo CPF/CNPJ -->
                <div class="grid gap-2">
                    <Label for="cpf">{{ form.tipo === 'Física' ? 'CPF' : 'CNPJ' }}</Label>
                    <Input
                        id="cpf"
                        type="text"
                        v-model="form.cpf"
                        @input="handleCpfInput"
                        required
                        :tabindex="3"
                        :placeholder="form.tipo === 'Física' ? '000.000.000-00' : '00.000.000/0000-00'"
                        :maxlength="form.tipo === 'Física' ? 14 : 18"
                    />
                    <InputError :message="form.errors.cpf" />
                </div>

                    <!-- Campo Telefone -->
                <div class="grid gap-2">
                    <Label for="telefone">Telefone</Label>
                    <Input
                        id="telefone"
                        type="tel"
                        v-model="form.telefone"
                        @input="handleTelefoneInput"
                        required
                        :tabindex="4"
                        placeholder="(00) 00000-0000"
                        :maxlength="15"
                    />
                    <InputError :message="form.errors.telefone" />
                </div>

                <!-- Campo Email -->
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        :tabindex="5"
                        placeholder="email@exemplo.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- Botão de Submissão -->
                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="6"
                    :disabled="form.processing"
                    data-test="submit-pessoa-button"
                >
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                    />
                    {{ form.id ? 'Atualizar' : 'Cadastrar' }}
                </Button>

            </form>
        </div>
    </AppLayout>
</template>
