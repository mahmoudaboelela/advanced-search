<template>
    <v-app>
        <v-container>
        <app-header></app-header>
        <v-main class="mt-3">
            <data-table></data-table>
        </v-main>
        </v-container>
        <v-footer border>2024

            All rights reserved</v-footer>
    </v-app>
</template>

<script setup>

import AppHeader from './components/AppHeader.vue';
import DataTable from './components/DataTable.vue';


</script>
