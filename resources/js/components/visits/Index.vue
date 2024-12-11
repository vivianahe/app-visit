<template>
    <div id="main-wrapper" class="mini-sidebar pb-16">
        <Loader :loading="loading" />
        <!--DataTable-->
        <DataTable class="tabla-m" :title="'Visitas'" :showSearch="true" :headers="headers" :items="dataVisit"
            :button_add="true" @click-add="dialogVisible = true, titleModal = 'AGREGAR VISITA'">
            <template v-slot:[`item.options`]="{ item }">
                <v-container>
                    <v-row align="center" justify="center">
                        <v-col cols="auto">
                            <v-tooltip text="Tooltip">
                                <template v-slot:activator="{ props }">
                                    <v-btn icon size="small" v-bind="props"
                                        @click="titleModal = 'EDITAR VISITA', dialogVisible = true, getVisitId(item.id)">
                                        <v-icon color="#a1a5b7">mdi mdi-file-document-edit</v-icon></v-btn>
                                </template>
                                <span>Editar</span>
                            </v-tooltip>
                        </v-col>
                        <v-col cols="auto">
                            <v-tooltip text="Tooltip">
                                <template v-slot:activator="{ props }">
                                    <v-btn icon size="small" v-bind="props" @click="confirmDeletion(item.id)">
                                        <v-icon color="#a1a5b7">mdi mdi-trash-can</v-icon>
                                    </v-btn>
                                </template>
                                <span>Eliminar</span>
                            </v-tooltip>
                        </v-col>
                    </v-row>
                </v-container>
            </template>
        </Datatable>
    </div>
    <!--Modal add or update-->
    <v-dialog width="1000" v-model="dialogVisible" persistent>
        <v-card :title=titleModal>
            <v-form @submit.prevent="setData">
                <v-container>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field :error-messages="errorMessages.name" v-model="visit.name" label="Nombre *"
                                required></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field :error-messages="errorMessages.email" v-model="visit.email" label="Email *"
                                type="text" required></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field :error-messages="errorMessages.latitude" v-model="visit.latitude"
                                label="Latitud *" type="text" @keypress="onlyNumbers" required></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field :error-messages="errorMessages.longitude" v-model="visit.longitude"
                                label="Longitud *" type="text" required></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <v-btn color="#0f172a" type="submit" class="mr-2">GUARDAR</v-btn>
                            <v-btn color="light" @click="clearFrm()">CANCELAR</v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import DataTable from '../utilities/Datatable.vue'
import Swal from 'sweetalert2';
import Loader from "../utilities/Loader.vue";
import { useRouter } from "vue-router";
const router = useRouter()
const dataVisit = ref([]);
const titleModal = ref('AGREGAR VISITA');
const errorMessages = ref({
    name: null,
    address: null,
    city: null,
    nit: null,
    total_rooms: null
});
const loading = ref(true);
const headers = ref([
    { title: "Nombre", align: "start", sortable: false, key: "name" },
    { title: "Email", align: "center", sortable: false, key: "email" },
    { title: "Latitud", align: "center", sortable: false, key: "latitude" },
    { title: "Longitud", align: "center", sortable: false, key: "longitude" },
    { title: "Acción", align: "center", sortable: false, key: "options" },
]);

const visit = ref({
    id: null,
    name: "",
    email: "",
    latitude: "",
    longitude: ""
});

const getVisitId = (item) => {
    loading.value = true;
    axios
        .get("/getVisitId/" + item)
        .then((response) => {
            visit.value.id = response.data.id;
            visit.value.name = response.data.name;
            visit.value.email = response.data.email;
            visit.value.latitude = response.data.latitude;
            visit.value.longitude = response.data.longitude;
            loading.value = false;
        })
        .catch((error) => {
            console.error(error);
            loading.value = false;
        });

};

const confirmDeletion = (id) => {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "¡Sí, bórralo!",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteVisit(id);
        }
    });
};
const deleteVisit = async (id) => {
    loading.value = true;
    axios
        .delete("/deleteVisit/" + id)
        .then((response) => {

            Swal.fire({
                title: "¡Borrado!",
                text: response.data.message,
                icon: "success",
            });
            loading.value = true;
            getVisits();

        })
        .catch((error) => {
            console.error(error);
            loading.value = false;
        });
};
const dialogVisible = ref(false);
const validateFields = () => {
    const param = visit.value;
    if (param.name.trim() === "") {
        errorMessages.value.name = "Este campo es obligatorio.";
        return false;
    } else {
        errorMessages.value.name = "";
    }
    if (param.email.trim() === "") {
        errorMessages.value.email = "Este campo es obligatorio.";
        return false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(param.email.trim())) {
        errorMessages.value.email = "Debe ingresar un correo válido.";
        return false;
    } else {
        errorMessages.value.email = "";
    }

    // Validación de latitud
    if (param.latitude.trim() === "") {
        errorMessages.value.latitude = "Este campo es obligatorio.";
        return false;
    } else if (!/^(-?[1-8]?\d(\.\d+)?|90(\.0+)?)$/.test(param.latitude.trim())) {
        errorMessages.value.latitude = "Debe ingresar una latitud válida (-90 a 90).";
        return false;
    } else {
        errorMessages.value.latitude = "";
    }

    // Validación de longitud
    if (param.longitude.trim() === "") {
        errorMessages.value.longitude = "Este campo es obligatorio.";
        return false;
    } else if (!/^(-?(1[0-7]\d|[1-9]?\d)(\.\d+)?|180(\.0+)?)$/.test(param.longitude.trim())) {
        errorMessages.value.longitude = "Debe ingresar una longitud válida (-180 a 180).";
        return false;
    } else {
        errorMessages.value.longitude = "";
    }

    return true;
};
const onlyNumbers = (event) => {
    let keyCode = event.keyCode || event.which;
    // Permitir números (0-9), el signo '-' y el carácter '.'
    if (
        (keyCode < 48 || keyCode > 57) && // Números
        keyCode !== 45 && // Signo '-'
        keyCode !== 46 // Carácter '.'
    ) {
        event.preventDefault();
    }
};

const getVisits = async () => {
    await axios
        .get("/getVisits")
        .then((response) => {
            dataVisit.value = response.data;
            loading.value = false;
        })
        .catch((error) => {
            console.error(error);
            loading.value = false;
        })
};

const setVisit = () => {
    if (!validateFields()) {
        return;
    }
    const formData = {
        id: visit.value.id,
        name: visit.value.name,
        email: visit.value.email,
        latitude: visit.value.latitude,
        longitude: visit.value.longitude
    };
    loading.value = true;
    axios
        .post('/setVisit', formData)
        .then((response) => {
            if (response.data.message) {
                Swal.fire("Correcto!", response.data.message, "success");
                clearFrm();
                getVisits();
            } else {
                Swal.fire(
                    "Atención!",
                    response.data.error,
                    "warning"
                );
                loading.value = false;
            }
        })
        .catch((error) => {
            loading.value = false;
            console.error(error);
        });

};

const setData = () => {
    if (titleModal.value === 'AGREGAR VISITA') {
        setVisit();
    } else {
        updateVisit();
    }
};

const updateVisit = () => {
    if (!validateFields()) {
        return;
    }
    const formData = {
        id: visit.value.id,
        name: visit.value.name,
        email: visit.value.email,
        latitude: visit.value.latitude,
        longitude: visit.value.longitude
    };

    loading.value = true;
    axios
        .post('/updateVisit', formData)
        .then((response) => {
            if (response.data.message) {
                Swal.fire("Correcto!", response.data.message, "success");
                clearFrm();
                getVisits();
            } else {
                Swal.fire(
                    "Atención!",
                    response.data.error,
                    "warning"
                );
                loading.value = false;
            }
        })
        .catch((error) => {
            loading.value = false;
            console.error(error);
        });

};
const clearFrm = () => {
    dialogVisible.value = false;
    visit.value.id = "";
    visit.value.name = "";
    visit.value.email = "";
    visit.value.latitude = "";
    visit.value.longitude = "";
    loading.value = false;
};

const getRedirection = async () => {
    const response = await axios.get(`/getInitialRedirectPath`);
    if (response.data != 'permission') {
        router.push({ path: response.data });
    }
}
onMounted(async () => {
    await getRedirection();
    await getVisits();
});

</script>
<style>
.card {
    border: none;
}

.card .card-body {
    background: #FFF;
}

.card-header {
    border: none;
}
</style>
