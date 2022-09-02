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
    movimientos: Object,
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

</script>

<template>
    <AppLayout :title="'Evento - ' + evento.nombre">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ evento.nombre }}
            </h2>
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
</template>
