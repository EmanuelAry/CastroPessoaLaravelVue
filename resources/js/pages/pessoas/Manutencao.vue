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
const form = useForm({
    id: props.pessoa?.id || '',
    nome: props.pessoa?.nome || '',
    cpf: props.pessoa?.cpf || '',
    tipo: props.pessoa?.tipo || 'Física',
    telefone: props.pessoa?.telefone || '',
    email: props.pessoa?.email || '',
});

const handleSubmit = () => {
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
                        required
                        :tabindex="3"
                        :placeholder="form.tipo === 'Física' ? '000.000.000-00' : '00.000.000/0000-00'"
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
                        required
                        :tabindex="4"
                        placeholder="(00) 00000-0000"
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
