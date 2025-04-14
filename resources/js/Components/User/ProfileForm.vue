<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card animated fadeIn w-100 p-3">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <div class="card-body">
                            <h4>Profile Update</h4>
                            <hr />
                            <div class="container-fluid m-0 p-0">
                                <div class="row m-0 p-0">
                                    <div class="col-md-4 p-2">
                                        <label>Name</label>
                                        <input v-model="form.username" placeholder="Name" class="form-control"
                                            type="text" />
                                    </div>

                                    <div class="col-md-4 p-2">
                                        <label>Email Address</label>
                                        <input v-model="form.email" disabled class="form-control" type="email" />
                                    </div>

                                    <div class="col-md-4 p-2">
                                        <label>Profile Picture</label>
                                        <input type="file" @change="handleFileChange" class="form-control" />
                                        <div v-if="preview" class="mt-2">
                                            <img :src="preview" class="rounded-circle" width="60" height="60" />
                                            
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-0 p-0">
                                    <div class="col-md-4 p-2">
                                        <button type="submit" class="btn mt-3 w-100 btn-success">Update</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";
import { ref } from "vue";

const toaster = createToaster();
const page = usePage();
const preview = ref(null);

const form = useForm({
    username: page.props.user.username || "",
    email: page.props.user.email || "",
    profile_pic: null
});

function handleFileChange(e) {
    const file = e.target.files[0];
    if (file) {
        form.profile_pic = file;
        preview.value = URL.createObjectURL(file);
    }
}

function submit() {
    if (!form.username.length) return toaster.error("Name is required");

    form.post("/user-update", {
        preserveScroll: true,
        forceFormData: true, // ⬅️ Important for image upload
        onSuccess: () => {
            if (page.props.flash.status) {
                toaster.success(page.props.flash.message);
                router.visit("/ProfilePage");
            } else {
                toaster.error(page.props.flash.message || "Update failed");
            }
        },
        onError: (err) => {
            toaster.error("Validation failed");
        }
    });
}
</script>
