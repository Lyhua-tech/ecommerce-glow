<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger>
            <slot name="trigger"/>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="start" :collision-padding="20">
            <slot name="content"/>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
