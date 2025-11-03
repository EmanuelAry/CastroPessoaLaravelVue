<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, UserPlus, UserRoundSearch } from 'lucide-vue-next';
import { BookUser, Linkedin, Github } from 'lucide-vue-next';
import pessoas from '@/routes/pessoas';

const mainNavItems: NavItem[] = [
    {
        title: 'Pessoas',
        href: '/pessoas',
        icon: UserRoundSearch,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'GitHub',
        href: 'https://github.com/EmanuelAry',
        icon: Github,
    },
    {
        title: 'LinkedIn',
        href: 'https://www.linkedin.com/in/emanuel-oliveira-4010841a2/',
        icon: Linkedin,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="'/pessoas'">
                            <BookUser />
                            Cadastro de Pessoas
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
