<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import JetButton from '@/Components/Button.vue';
import JetConfirmationModal from '@/Components/ConfirmationModal.vue';
import JetDangerButton from '@/Components/DangerButton.vue';
import JetDialogModal from '@/Components/DialogModal.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetLabel from '@/Components/Label.vue';
import JetSecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    eventos: Array,
});

const unirseForm = useForm();
const eventoAUnirse = ref(null);

const confirmUnirse = (evento) => {
    eventoAUnirse.value = evento;
};

const unirse = () => {
    unirseForm.post(route('eventos.unirse', eventoAUnirse.value.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (eventoAUnirse.value = null),
    });
};

</script>

<template>
    <AppLayout title="Eventos">
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Eventos
            </h2>
        </template> -->

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <template v-for="evento in eventos">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-5">

                        <div class="flex justify-between">
                            <h1 class="p-3">{{ evento.nombre }}</h1>

                            <button v-if="!evento.usuario_pertenece" type="button" @click="confirmUnirse(evento)"
                                class="mt-2 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                Unirse
                            </button>

                            <button v-if="evento.usuario_pertenece" type="button"
                                class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Nuevo Gasto
                            </button>
                        </div>

                        <div class="overflow-x-auto relative">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">
                                            Nombre
                                        </th>
                                        <th scope="col" class="py-3 px-6 text-end">
                                            Gasto
                                        </th>
                                        <th scope="col" class="py-3 px-6 text-end">
                                            Balance
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in evento.users"
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row"
                                            class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ item.name }}
                                        </th>
                                        <td class="py-4 px-6 text-end">
                                            {{ item.gastos }}
                                        </td>
                                        <td :class="{ 'text-green-600': item.color == 'green', 'text-red-600': item.color == 'red' }"
                                            class="py-4 px-6 text-end">
                                            {{ item.balance }}
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-center gap-5 my-3">
                            <div>Gastos Totales: <span class="font-bold">${{evento.gastos}}</span></div>
                            <div>Gastos por Usuario: <span class="font-bold">${{evento.gastos_usuario}}</span></div>
                        </div>

                    </div>
                </template>
            </div>
        </div>
    </AppLayout>

    <!-- Unirse Confirmation Modal -->
    <JetConfirmationModal :show="eventoAUnirse != null" @close="eventoAUnirse = null">
        <template #title>
            Unirse a {{eventoAUnirse.nombre}}
        </template>

        <template #content>
            Estas seguro de unirse a este evento?
            Los gastos se dividiran con todos sus integrantes por igual
        </template>

        <template #footer>
            <JetSecondaryButton @click="eventoAUnirse = null">
                Cancelar
            </JetSecondaryButton>

            <JetDangerButton class="ml-3" :class="{ 'opacity-25': unirseForm.processing }"
                :disabled="unirseForm.processing" @click="unirse">
                Unirse
            </JetDangerButton>
        </template>
    </JetConfirmationModal>
</template>
