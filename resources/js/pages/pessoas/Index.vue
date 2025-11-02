<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Bug, LaptopMinimalCheck, MessageCircleWarning } from 'lucide-vue-next';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

interface Pessoas {
    id: number;
    nome: string;
    cpf: string;
    tipo: string;
    telefone: string;
    email: string;
}

interface Props {
    pessoas: Pessoas[]
}

// Get props from inertia
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pessoas',
        href: '/pessoas',
    },
];

const page = usePage();

const handleDelete = (id: number) => {

    if (confirm('Tem certeza que deseja deletar esta pessoa?')) {
        router.delete(`/pessoas/${id}`);
    }
};

</script>

<template>
    <Head title="Pessoas" />
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
            <Link href="/pessoas/manutencao">
                <Button>
                    Cadastrar Nova Pessoa
                </Button>
            </Link>
        </div>
        <div>
            <Table>
                <TableCaption>Lista de Pessoas</TableCaption>
                <TableHeader>
                <TableRow>
                    <TableHead class="w-[100px]">ID</TableHead>
                    <TableHead>Nome</TableHead>
                    <TableHead>CPF/CNPJ</TableHead>
                    <TableHead>Tipo</TableHead>
                    <TableHead>Telefone</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead class="text-center">Ações</TableHead>
                </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="pessoa in props.pessoas" :key="pessoa.id">
                        <TableCell>{{ pessoa.id }}</TableCell>
                        <TableCell>{{ pessoa.nome }}</TableCell>
                        <TableCell>{{ pessoa.cpf }}</TableCell>
                        <TableCell>{{ pessoa.tipo }}</TableCell>
                        <TableCell>{{ pessoa.telefone }}</TableCell>
                        <TableCell>{{ pessoa.email }}</TableCell>
                        <TableCell class="text-center space-x-2">
                            <Link :href="`/pessoas/edit/${pessoa.id}`">
                                <Button variant="outline" size="sm">
                                    Editar
                                </Button>
                            </Link>                            
                            <Button variant="outline" size="sm" @click="handleDelete(pessoa.id)">
                                Deletar
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
