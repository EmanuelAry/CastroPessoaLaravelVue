<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Bug, LaptopMinimalCheck, MessageCircleWarning, Trash, Pencil, UserPlus } from 'lucide-vue-next';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { computed, ref } from 'vue';

interface Pessoa {
  id: number;
  nome: string;
  cpf: string;
  tipo: string;
  telefone: string;
  email: string;
}

interface Props {
  pessoas: Pessoa[];
}

const props = defineProps<Props>();
const page = usePage();

const deletingId = ref<number | null>(null);

// Computed properties
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  {
    title: 'Pessoas',
    href: '/pessoas',
  },
]);

const isEmpty = computed(() => props.pessoas.length === 0);

const flashMessages = computed(() => ({
  message: page.props.flash?.message,
  success: page.props.flash?.success,
  error: page.props.flash?.error,
}));

// Methods
const handleDelete = async (id: number) => {
  if (!confirm('Tem certeza que deseja excluir esta pessoa?')) {
    return;
  }

  deletingId.value = id;

  try {
    await router.delete(`/pessoas/${id}`);
  } catch (error) {
    console.error('Erro ao excluir pessoa:', error);
  } finally {
    deletingId.value = null;
  }
};

const formatCPF = (cpf: string) => {
  return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
};

const formatPhone = (phone: string) => {
  return phone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
};
</script>

<template>
  <Head title="Pessoas" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container mx-auto p-4 space-y-6">
      <!-- Notificações Flash -->
      <div v-if="flashMessages.message" class="mb-4">
        <Alert variant="default">
          <MessageCircleWarning class="h-4 w-4" />
          <AlertTitle>Notificação</AlertTitle>
          <AlertDescription>
            {{ flashMessages.message }}
          </AlertDescription>
        </Alert>
      </div>

      <div v-if="flashMessages.success" class="mb-4">
        <Alert variant="success">
          <LaptopMinimalCheck class="h-4 w-4" />
          <AlertTitle>Sucesso</AlertTitle>
          <AlertDescription>
            {{ flashMessages.success }}
          </AlertDescription>
        </Alert>
      </div>

      <div v-if="flashMessages.error" class="mb-4">
        <Alert variant="destructive">
          <Bug class="h-4 w-4" />
          <AlertTitle>Erro</AlertTitle>
          <AlertDescription>
            {{ flashMessages.error }}
          </AlertDescription>
        </Alert>
      </div>

      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Pessoas</h1>
          <p class="text-gray-600">Gerencie os registros de pessoas do sistema</p>
        </div>
        
        <Link href="/pessoas/manutencao">
          <Button class="gap-2">
            <UserPlus class="h-4 w-4" />
            Nova Pessoa
          </Button>
        </Link>
      </div>

      <div class="border rounded-lg bg-white shadow-sm">
        <Table>
          <TableCaption>Lista de pessoas cadastradas no sistema</TableCaption>
          <TableHeader>
            <TableRow>
              <TableHead class="w-20">ID</TableHead>
              <TableHead>Nome</TableHead>
              <TableHead>CPF/CNPJ</TableHead>
              <TableHead>Tipo</TableHead>
              <TableHead>Telefone</TableHead>
              <TableHead>Email</TableHead>
              <TableHead class="w-32 text-center">Ações</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-if="isEmpty">
              <TableCell colspan="7" class="text-center py-8 text-gray-500">
                Nenhuma pessoa cadastrada
              </TableCell>
            </TableRow>

            <TableRow
              v-for="pessoa in props.pessoas"
              v-else
              :key="pessoa.id"
              class="hover:bg-gray-50/50 transition-colors"
            >
              <TableCell class="font-medium">{{ pessoa.id }}</TableCell>
              <TableCell class="font-medium">{{ pessoa.nome }}</TableCell>
              <TableCell>{{ formatCPF(pessoa.cpf) }}</TableCell>
              <TableCell>
                <span 
                  :class="[
                    'px-2 py-1 rounded-full text-xs font-medium',
                    pessoa.tipo === 'Física' 
                      ? 'bg-blue-100 text-blue-800' 
                      : 'bg-green-100 text-green-800'
                  ]"
                >
                  {{ pessoa.tipo }}
                </span>
              </TableCell>
              <TableCell>{{ formatPhone(pessoa.telefone) }}</TableCell>
              <TableCell class="truncate max-w-[200px]">{{ pessoa.email }}</TableCell>
              <TableCell>
                <div class="flex justify-center gap-2">
                  <Link :href="`/pessoas/edit/${pessoa.id}`">
                    <Button variant="outline" size="sm" class="gap-1">
                      <Pencil class="h-3 w-3" />
                      Editar
                    </Button>
                  </Link>
                  
                  <Button 
                    variant="outline" 
                    size="sm" 
                    class="gap-1 text-red-600 hover:text-red-700 hover:bg-red-50"
                    @click="handleDelete(pessoa.id)"
                    :disabled="deletingId === pessoa.id"
                  >
                    <Trash class="h-3 w-3" />
                    {{ deletingId === pessoa.id ? 'Excluindo...' : 'Excluir' }}
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>