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
import { LoaderCircle, ArrowLeft } from 'lucide-vue-next';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Bug, LaptopMinimalCheck, MessageCircleWarning } from 'lucide-vue-next';
import { watch, computed, ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

interface Pessoa {
  id?: number;
  nome: string;
  cpf: string;
  tipo: string;
  telefone: string;
  email: string;
}

interface Props {
  pessoa?: Pessoa;
}

const DOCUMENT_LENGTHS = {
  cpf: 14,
  cnpj: 18
} as const;

const PHONE_LENGTH = 15;

const props = defineProps<Props>();
const page = usePage();
const isSubmitting = ref(false);

const title = computed(() => props.pessoa?.id ? 'Editar Pessoa' : 'Cadastrar Pessoa');
const submitButtonText = computed(() => 
  isSubmitting.value 
    ? props.pessoa?.id ? 'Atualizando...' : 'Cadastrando...'
    : props.pessoa?.id ? 'Atualizar' : 'Cadastrar'
);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Pessoas', href: '/pessoas' },
  { title: title.value, href: '/pessoas/manutencao' },
]);

const flashMessages = computed(() => ({
  message: page.props.flash?.message,
  success: page.props.flash?.success,
  error: page.props.flash?.error,
}));

const validarCPF = (cpf: string): boolean => {
  cpf = cpf.replace(/\D/g, '');
  
  if (cpf.length !== 11) return false;
  if (/^(\d)\1+$/.test(cpf)) return false;
  
  let soma = 0;
  for (let i = 0; i < 9; i++) {
    soma += parseInt(cpf.charAt(i)) * (10 - i);
  }
  let resto = 11 - (soma % 11);
  const digitoVerificador1 = resto >= 10 ? 0 : resto;
  
  if (digitoVerificador1 !== parseInt(cpf.charAt(9))) return false;
  
  soma = 0;
  for (let i = 0; i < 10; i++) {
    soma += parseInt(cpf.charAt(i)) * (11 - i);
  }
  resto = 11 - (soma % 11);
  const digitoVerificador2 = resto >= 10 ? 0 : resto;
  
  return digitoVerificador2 === parseInt(cpf.charAt(10));
};

const validarCNPJ = (cnpj: string): boolean => {
  cnpj = cnpj.replace(/\D/g, '');
  
  if (cnpj.length !== 14) return false;
  if (/^(\d)\1+$/.test(cnpj)) return false;
  
  const tamanho = cnpj.length - 2;
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
  
  const novoTamanho = tamanho + 1;
  numeros = cnpj.substring(0, novoTamanho);
  soma = 0;
  pos = novoTamanho - 7;
  
  for (let i = novoTamanho; i >= 1; i--) {
    soma += parseInt(numeros.charAt(novoTamanho - i)) * pos--;
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

const aplicarMascaraCPF = (cpf: string): string => {
  return cpf
    .replace(/\D/g, '')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d{1,2})$/, '$1-$2')
    .slice(0, DOCUMENT_LENGTHS.cpf);
};

const aplicarMascaraCNPJ = (cnpj: string): string => {
  return cnpj
    .replace(/\D/g, '')
    .replace(/^(\d{2})(\d)/, '$1.$2')
    .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
    .replace(/\.(\d{3})(\d)/, '.$1/$2')
    .replace(/(\d{4})(\d)/, '$1-$2')
    .slice(0, DOCUMENT_LENGTHS.cnpj);
};

const aplicarMascaraDocumento = (valor: string, tipo: string): string => {
  if (!valor) return '';
  
  const apenasNumeros = valor.replace(/\D/g, '');
  
  if (tipo === 'Física') {
    return aplicarMascaraCPF(apenasNumeros);
  } else {
    return aplicarMascaraCNPJ(apenasNumeros);
  }
};

const aplicarMascaraTelefone = (telefone: string): string => {
  const apenasNumeros = telefone.replace(/\D/g, '');
  
  if (apenasNumeros.length === 11) {
    return apenasNumeros
      .replace(/(\d{2})(\d)/, '($1) $2')
      .replace(/(\d{5})(\d{4})/, '$1-$2')
      .slice(0, PHONE_LENGTH);
  } else if (apenasNumeros.length === 10) {
    return apenasNumeros
      .replace(/(\d{2})(\d)/, '($1) $2')
      .replace(/(\d{4})(\d{4})/, '$1-$2')
      .slice(0, PHONE_LENGTH);
  }
  
  return telefone;
};

const form = useForm({
  id: props.pessoa?.id || '',
  nome: props.pessoa?.nome || '',
  cpf: props.pessoa?.cpf || '',
  tipo: props.pessoa?.tipo || 'Física',
  telefone: props.pessoa?.telefone || '',
  email: props.pessoa?.email || '',
});

const handleDocumentoInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const valor = target.value;
  form.cpf = aplicarMascaraDocumento(valor, form.tipo);
};

const handleTelefoneInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const valor = target.value;
  form.telefone = aplicarMascaraTelefone(valor);
};

const handleSubmit = async () => {
  if (isSubmitting.value) return;
  
  // Remove máscara para validação
  const documentoLimpo = form.cpf.replace(/\D/g, '');
  
  // Valida o documento baseado no tipo
  if (!validarDocumento(documentoLimpo, form.tipo)) {
    form.setError('cpf', form.tipo === 'Física' ? 'CPF inválido' : 'CNPJ inválido');
    return;
  }

  isSubmitting.value = true;

  try {
    if (form.id) {
      await form.put(`/pessoas/edit/${props.pessoa?.id}`);
    //   await form.put(route('pessoas.update', { pessoa: props.pessoa?.id })); <-- route() não funciona aqui (verificar)
    } else {
      await form.post(`/pessoas`);
    //   await form.post(route('pessoas.store')); <-- route() não funciona aqui (verificar)
    }
  } catch (error) {
    console.error('Erro ao salvar pessoa:', error);
  } finally {
    isSubmitting.value = false;
  }
};

watch(() => form.tipo, (novoTipo, antigoValor) => {
  // Reaplica a máscara quando o tipo muda
  form.cpf = aplicarMascaraDocumento(form.cpf, novoTipo);
  
  // Limpa erro do CPF/CNPJ ao mudar o tipo
  if (form.errors.cpf) {
    form.clearErrors('cpf');
  }
});

onMounted(() => {
  // Aplica máscaras iniciais se houver dados
  if (form.cpf) {
    form.cpf = aplicarMascaraDocumento(form.cpf, form.tipo);
  }
  if (form.telefone) {
    form.telefone = aplicarMascaraTelefone(form.telefone);
  }
});
</script>

<template>
  <Head :title="title" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container mx-auto p-4 space-y-6 max-w-4xl">
      <!-- Header -->
      <div class="flex items-center gap-4">
        <Link href="/pessoas" class="text-gray-600 hover:text-gray-900 transition-colors">
          <ArrowLeft class="h-5 w-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
          <p class="text-gray-600">
            {{ props.pessoa?.id ? 'Edite os dados da pessoa' : 'Preencha os dados para cadastrar uma nova pessoa' }}
          </p>
        </div>
      </div>

      <!-- Flash Messages -->
      <div class="space-y-3">
        <Alert 
          v-if="flashMessages.message" 
          variant="default"
          class="animate-fade-in"
        >
          <MessageCircleWarning class="h-4 w-4" />
          <AlertTitle>Notificação</AlertTitle>
          <AlertDescription>
            {{ flashMessages.message }}
          </AlertDescription>
        </Alert>

        <Alert 
          v-if="flashMessages.success" 
          variant="success"
          class="animate-fade-in"
        >
          <LaptopMinimalCheck class="h-4 w-4" />
          <AlertTitle>Sucesso</AlertTitle>
          <AlertDescription>
            {{ flashMessages.success }}
          </AlertDescription>
        </Alert>

        <Alert 
          v-if="flashMessages.error" 
          variant="destructive"
          class="animate-fade-in"
        >
          <Bug class="h-4 w-4" />
          <AlertTitle>Erro</AlertTitle>
          <AlertDescription>
            {{ flashMessages.error }}
          </AlertDescription>
        </Alert>
      </div>

      <!-- Form -->
      <div class="bg-white border rounded-lg shadow-sm p-6">
        <form class="space-y-6" @submit.prevent="handleSubmit">
          <!-- Campo ID (visível apenas em edição) -->
          <div v-if="form.id" class="grid gap-2 max-w-xs">
            <Label for="id">ID</Label>
            <Input
              id="id"
              type="text"
              v-model="form.id"
              disabled
              class="bg-gray-50"
            />
            <InputError :message="form.errors.id" />
          </div>

          <!-- Campo Nome -->
          <div class="grid gap-2">
            <Label for="nome" class="required">Nome Completo</Label>
            <Input
              id="nome"
              type="text"
              v-model="form.nome"
              required
              autofocus
              :tabindex="1"
              placeholder="Digite o nome completo"
              :disabled="isSubmitting"
              :class="{ 'border-red-500': form.errors.nome }"
            />
            <InputError :message="form.errors.nome" />
          </div>
          
          <!-- Campo Tipo (Pessoa Física/Jurídica) -->
          <div class="grid gap-2 max-w-xs">
            <Label for="tipo" class="required">Tipo de Pessoa</Label>
            <Select
              id="tipo"
              v-model="form.tipo"
              :tabindex="2"
              :disabled="isSubmitting"
            >
              <option value="Física">Pessoa Física</option>
              <option value="Jurídica">Pessoa Jurídica</option>
            </Select>
            <InputError :message="form.errors.tipo" />
          </div>

          <!-- Campo CPF/CNPJ -->
          <div class="grid gap-2 max-w-md">
            <Label for="cpf" class="required">
              {{ form.tipo === 'Física' ? 'CPF' : 'CNPJ' }}
            </Label>
            <Input
              id="cpf"
              type="text"
              v-model="form.cpf"
              @input="handleDocumentoInput"
              required
              :tabindex="3"
              :placeholder="form.tipo === 'Física' ? '000.000.000-00' : '00.000.000/0000-00'"
              :maxlength="form.tipo === 'Física' ? DOCUMENT_LENGTHS.cpf : DOCUMENT_LENGTHS.cnpj"
              :disabled="isSubmitting"
              :class="{ 'border-red-500': form.errors.cpf }"
            />
            <InputError :message="form.errors.cpf" />
          </div>

          <!-- Campo Telefone -->
          <div class="grid gap-2 max-w-md">
            <Label for="telefone" class="required">Telefone</Label>
            <Input
              id="telefone"
              type="tel"
              v-model="form.telefone"
              @input="handleTelefoneInput"
              required
              :tabindex="4"
              placeholder="(00) 00000-0000"
              :maxlength="PHONE_LENGTH"
              :disabled="isSubmitting"
              :class="{ 'border-red-500': form.errors.telefone }"
            />
            <InputError :message="form.errors.telefone" />
          </div>

          <!-- Campo Email -->
          <div class="grid gap-2 max-w-md">
            <Label for="email" class="required">Email</Label>
            <Input
              id="email"
              type="email"
              v-model="form.email"
              required
              :tabindex="5"
              placeholder="email@exemplo.com"
              :disabled="isSubmitting"
              :class="{ 'border-red-500': form.errors.email }"
            />
            <InputError :message="form.errors.email" />
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4 border-t">
            <Button
              type="submit"
              :tabindex="6"
              :disabled="isSubmitting || !form.isDirty"
              class="min-w-32"
              data-test="submit-pessoa-button"
            >
              <LoaderCircle
                v-if="isSubmitting"
                class="h-4 w-4 animate-spin mr-2"
              />
              {{ submitButtonText }}
            </Button>
            
            <Link href="/pessoas">
              <Button
                type="button"
                variant="outline"
                :disabled="isSubmitting"
                :tabindex="7"
              >
                Cancelar
              </Button>
            </Link>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.required::after {
  content: " *";
  color: #ef4444;
}
</style>