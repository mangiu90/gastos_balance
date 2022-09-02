<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { computed, ref } from 'vue';
import Pagination from '../Components/Pagination.vue';
import JetButton from '@/Components/Button.vue';
import JetConfirmationModal from '@/Components/ConfirmationModal.vue';
import JetDangerButton from '@/Components/DangerButton.vue';
import JetDialogModal from '@/Components/DialogModal.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetLabel from '@/Components/Label.vue';
import JetSecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    evento: Object,
    gastos: String,
    gastos_usuario: String,
    usuario_pertenece: Boolean,
    movimientos: Object,
    tipo_options: Object,
});

const user_id = computed(() => usePage().props.value.user?.id);

//editar movimiento modal
const movimientoAEditar = ref(null);

const editarMovimientoForm = useForm({
    monto: '',
    detalle: '',
});

const abrirEditarMovimiento = (movimiento) => {
    movimientoAEditar.value = movimiento;
    editarMovimientoForm.monto = movimiento.monto;
    editarMovimientoForm.detalle = movimiento.detalle;
};

const nuevoMovimiento = () => {
    editarMovimientoForm.post(route('movimiento.editar', movimientoAEditar.value.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => cerrarEditarMovimientoModal(),
        onFinish: () => cerrarEditarMovimientoModal(),
    });
};

const cerrarEditarMovimientoModal = () => {
    movimientoAEditar.value = null
    editarMovimientoForm.reset()
}

//eliminar movimiento modal
const eliminarMovimientoForm = useForm();
const movimientoAEliminar = ref(null);

const confirmarEliminarMovimiento = (movimiento) => {
    movimientoAEliminar.value = movimiento;
};

const eliminarMovimiento = () => {
    eliminarMovimientoForm.delete(route('movimiento.eliminar', movimientoAEliminar.value.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (movimientoAEliminar.value = null),
    });
};

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

//eliminar movimiento modal
const salirForm = useForm();
const eventoASalir = ref(null);

const confirmarSalirEvento = (evento) => {
    eventoASalir.value = evento;
};

const salirEvento = () => {
    salirForm.post(route('eventos.salir', eventoASalir.value.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (eventoASalir.value = null),
    });
};

</script>

<template>
    <AppLayout :title="'Evento - ' + evento.nombre">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ evento.nombre }}
                </h2>
                <div>
                    <button v-if="!usuario_pertenece" type="button" @click="confirmUnirse(evento)"
                        class="mt-2 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        Unirse
                    </button>

                    <button v-if="usuario_pertenece" type="button" @click="confirmNuevoGasto(evento)"
                        class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Nuevo Gasto
                    </button>

                    <button v-if="usuario_pertenece" type="button" @click="confirmarSalirEvento(evento)"
                        class="mt-2 focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                        Salir
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-center gap-5 my-3">
                    <div>Gastos Totales: <span class="font-bold">${{ gastos }}</span></div>
                    <div>Gastos por Usuario: <span class="font-bold">${{ gastos_usuario }}</span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-5">
                        <div class="overflow-x-auto relative">
                            <table class="w-full text-sm text-left text-gray-900 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">
                                            Fecha
                                        </th>
                                        <th scope="col" class="py-3 px-6">
                                            Usuario
                                        </th>
                                        <th scope="col" class="py-3 px-6">
                                            Detalle
                                        </th>
                                        <th scope="col" class="py-3 px-6 text-end">
                                            Monto
                                        </th>
                                        <th scope="col" class="py-3 px-6 text-end">

                                        </th>
                                        <th scope="col" class="py-3 px-6 text-end">

                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="movimientos && movimientos.data" v-for="movimiento in movimientos.data"
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row"
                                            class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ movimiento.fecha }}
                                        </th>
                                        <td class="py-4 px-6">
                                            {{ movimiento.user_name }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ movimiento.detalle }}
                                        </td>
                                        <td class="py-4 px-6 text-end"
                                            :class="{ 'text-green-800': movimiento.tipo == 'EGRESO', 'text-red-800': movimiento.tipo == 'INGRESO' }">
                                            {{ movimiento.monto_format }}
                                        </td>
                                        <td class="py-4 px-6 text-end" v-if="movimiento.user_id == user_id">
                                            <i class="fa-solid fa-pencil cursor-pointer"
                                                @click="abrirEditarMovimiento(movimiento)"></i>
                                        </td>
                                        <td class="py-4 px-6 text-end" v-if="movimiento.user_id == user_id">
                                            <i class="fa-solid fa-trash text-red-600 cursor-pointer"
                                                @click="confirmarEliminarMovimiento(movimiento)"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination class="m-5" :links="movimientos.links" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Editar Movimiento Modal -->
    <JetDialogModal :show="movimientoAEditar != null" @close="cerrarEditarMovimientoModal">
        <template #title>
            Editar
        </template>

        <template #content>
            <div class="grid grid-cols-1">
                <div class="">
                    <JetLabel for="monto" value="Monto" />
                    <JetInput id="monto" v-model="editarMovimientoForm.monto" type="number"
                        class="mt-1 block w-full text-end" />
                    <JetInputError class="mt-2" :message="editarMovimientoForm.errors.monto" />
                </div>
                <div class="mt-4">
                    <JetLabel for="detalle" value="Detalle" />
                    <JetInput id="detalle" v-model="editarMovimientoForm.detalle" type="text"
                        class="mt-1 block w-full" />
                    <JetInputError class="mt-2" :message="editarMovimientoForm.errors.detalle" />
                </div>
            </div>
        </template>

        <template #footer>
            <JetSecondaryButton @click="cerrarEditarMovimientoModal">
                Cancelar
            </JetSecondaryButton>

            <JetButton class="ml-3" :class="{ 'opacity-25': editarMovimientoForm.processing }"
                :disabled="editarMovimientoForm.processing" @click="nuevoMovimiento">
                Guardar
            </JetButton>
        </template>
    </JetDialogModal>

    <!-- Eliminar Movimiento Modal -->
    <JetConfirmationModal :show="movimientoAEliminar != null" @close="movimientoAEliminar = null">
        <template #title>
            Eliminar Movimiento
        </template>

        <template #content>
            Estas seguro de querer eliminar este registro?
        </template>

        <template #footer>
            <JetSecondaryButton @click="movimientoAEliminar = null">
                Cancelar
            </JetSecondaryButton>

            <JetDangerButton class="ml-3" :class="{ 'opacity-25': eliminarMovimientoForm.processing }"
                :disabled="eliminarMovimientoForm.processing" @click="eliminarMovimiento">
                Eliminar
            </JetDangerButton>
        </template>
    </JetConfirmationModal>

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

    <!-- Salir Modal -->
    <JetConfirmationModal :show="eventoASalir != null" @close="eventoASalir = null">
        <template #title>
            Salir de {{ eventoASalir.nombre }}
        </template>

        <template #content>
            Estas seguro de querer salir de este evento?
            Se borraran todos tus registros de este  evento.
        </template>

        <template #footer>
            <JetSecondaryButton @click="eventoASalir = null">
                Cancelar
            </JetSecondaryButton>

            <JetDangerButton class="ml-3" :class="{ 'opacity-25': salirForm.processing }"
                :disabled="salirForm.processing" @click="salirEvento">
                Salir
            </JetDangerButton>
        </template>
    </JetConfirmationModal>
</template>
