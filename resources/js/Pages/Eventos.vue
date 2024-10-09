<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/inertia-vue3';
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
    tipo_options: Object,
});

//unirse modal
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

//nuevo gasto modal
const nuevoGastoForm = useForm({
    tipo: 'INGRESO',
    monto: '',
    detalle: '',
});
const eventoAGastar = ref(null);

const confirmNuevoGasto = (evento) => {
    eventoAGastar.value = evento;
};

const nuevoGasto = () => {
    nuevoGastoForm.post(route('eventos.nuevo-gasto', eventoAGastar.value.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => cerrarNuevoGastoModal(),
        onFinish: () => cerrarNuevoGastoModal(),
    });
};

const cerrarNuevoGastoModal = () => {
    eventoAGastar.value = null
    nuevoGastoForm.reset()
}

//crear evento modal
const crearEventoForm = useForm({
    nombre: '',
});
const mostrarCrearEventoModal = ref(false);
const nombreInput = ref(null);

const confirmCrearEvento = () => {
    mostrarCrearEventoModal.value = true;
    setTimeout(() => nombreInput.value.focus(), 250);
};

const crearEvento = () => {
    crearEventoForm.post(route('eventos.crear'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (mostrarCrearEventoModal.value = false),
        onFinish: () => crearEventoForm.reset(),
    });
};

const cerrarCrearEventoModal = () => {
    mostrarCrearEventoModal.value = false
    crearEventoForm.reset()
}

</script>

<template>
    <AppLayout title="Eventos">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Eventos
                </h2>

                <JetButton @click="confirmCrearEvento">Nuevo</JetButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <template v-for="evento in eventos">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-5">

                        <div class="flex justify-between">
                            <Link :href="route('evento.detalle', evento.id)">
                            <h1 class="p-3 hover:text-blue-800">{{ evento.nombre }} <i class="fa-solid fa-link text-blue-600"></i></h1>
                            </Link>

                            <button v-if="!evento.usuario_pertenece" type="button" @click="confirmUnirse(evento)"
                                class="mt-2 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                Unirse
                            </button>

                            <button v-if="evento.usuario_pertenece" type="button" @click="confirmNuevoGasto(evento)"
                                class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Nuevo Gasto
                            </button>
                        </div>

                        <div class="overflow-x-auto relative">
                            <table class="w-full text-sm text-left text-gray-500 ">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50  ">
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
                                        class="bg-white border-b ">
                                        <th scope="row"
                                            class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap ">
                                            {{ item.name }}
                                        </th>
                                        <td class="py-4 px-6 text-end">
                                            {{ item.gastos }}
                                        </td>
                                        <td :class="{ 'text-green-800': item.color == 'green', 'text-red-800': item.color == 'red' }"
                                            class="py-4 px-6 text-end">
                                            {{ item.balance }}
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-center gap-5 my-3">
                            <div>Gastos Totales: <span class="font-bold">${{ evento.gastos }}</span></div>
                            <div>Gastos por Usuario: <span class="font-bold">${{ evento.gastos_usuario }}</span></div>
                        </div>

                    </div>
                </template>
            </div>
        </div>
    </AppLayout>

    <!-- Unirse Confirmation Modal -->
    <JetConfirmationModal :show="eventoAUnirse != null" @close="eventoAUnirse = null">
        <template #title>
            Unirse a {{ eventoAUnirse.nombre }}
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

    <!-- Nuevo Gasto Modal -->
    <JetDialogModal :show="eventoAGastar != null" @close="cerrarNuevoGastoModal">
        <template #title>
            Nuevo Gasto para {{ eventoAGastar.nombre }}
        </template>

        <template #content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <JetLabel for="tipo" value="Tipo" class="mb-1" />
                    <select id="tipo" v-model="nuevoGastoForm.tipo" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option v-for="tipo_option, key in tipo_options" :value="key">{{ tipo_option }}</option>
                    </select>
                </div>
                <div class="">
                    <JetLabel for="monto" value="Monto" />
                    <JetInput id="monto" v-model="nuevoGastoForm.monto" type="number"
                        class="mt-1 block w-full text-end" />
                    <JetInputError class="mt-2" :message="nuevoGastoForm.errors.monto" />
                </div>
                <div class="mt-4 col-span-2">
                    <JetLabel for="detalle" value="Detalle" />
                    <JetInput id="detalle" v-model="nuevoGastoForm.detalle" type="text" class="mt-1 block w-full" />
                    <JetInputError class="mt-2" :message="nuevoGastoForm.errors.detalle" />
                </div>
            </div>
        </template>

        <template #footer>
            <JetSecondaryButton @click="cerrarNuevoGastoModal">
                Cancelar
            </JetSecondaryButton>

            <JetButton class="ml-3" :class="{ 'opacity-25': nuevoGastoForm.processing }"
                :disabled="nuevoGastoForm.processing" @click="nuevoGasto">
                Guardar
            </JetButton>
        </template>
    </JetDialogModal>

    <!-- Nuevo Evento Modal -->
    <JetDialogModal :show="mostrarCrearEventoModal" @close="cerrarCrearEventoModal">
        <template #title>
            Crear nuevo evento
        </template>

        <template #content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mt-4 col-span-2">
                    <JetLabel for="nombre" value="Nombre" />
                    <JetInput id="nombre" v-model="crearEventoForm.nombre" type="text" class="mt-1 block w-full"
                        @keyup.enter="crearEvento" ref="nombreInput" />
                    <JetInputError class="mt-2" :message="crearEventoForm.errors.nombre" />
                </div>
            </div>
        </template>

        <template #footer>
            <JetSecondaryButton @click="cerrarCrearEventoModal">
                Cancelar
            </JetSecondaryButton>

            <JetButton class="ml-3" :class="{ 'opacity-25': crearEventoForm.processing }"
                :disabled="crearEventoForm.processing" @click="crearEvento">
                Guardar
            </JetButton>
        </template>
    </JetDialogModal>
</template>
