<template>
  <div class="relative flex items-top justify-center py-5 overlay-bg">
        <div class="card shadow p-2 prediction-card col-lg-6 col-md-6">
            <div class="card-body">
            <h3 class="text-center">Prediction</h3>
            <fieldset>
                <legend class="w-auto"><h5>Images</h5></legend>
                <form @submit="proceed">
                <div class="form-group form-inline">
                    <label class="col-sm-2 text-left p-0">Company</label>
                    <input type="text" class="form-control col-sm-8" v-model="company_name">
                </div>

                <div class="form-group form-inline">
                    <label class="col-sm-2 text-left p-0">Image</label>
                    <input type="file" ref="file" id="file-upload" accept="image" @change="uploadImage">
                    <!-- <input type="file" ref="file" id="file-upload" @change="uploadImage"> -->
                </div>
                    <!-- {{ user.name }} -->
                <div class="form-group form-inline">
                    <label class="col-sm-2 text-left p-0">Format</label>
                    <select class="form-control col-sm-8" v-model="format">
                        <option value=""> Select Output Format</option>
                        <option value="csv">CSV</option>
                        <option value="json">JSON</option>
                    </select>
                </div>

                <div id="imagePreviewBox" v-if="imageDataUrl" >
                    <img :src="imageDataUrl" class="imagePreview">
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="w-50">
                        <progress max="100" :value.prop="percentCompleted"></progress>
                    </div>
                    <div class="w-50 text-right">
                        <button class="btn btn-primary" type="submit">Start Extraction</button>
                    </div>
                </div>
                </form>
            </fieldset>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    // props: ['user'],
    data() {
        return {
            image: '',
            imageDataUrl: '',
            company_name: '',
            format: '',
            percentCompleted: 0,
        }
    },
    mounted(){
        // console.log(this.user);
    },
    methods: {
        uploadImage(e) {
            this.image = this.$refs.file.files[0];
            // console.log(this.image);
            const image = e.target.files[0];
            const reader = new FileReader();
            reader.readAsDataURL(image);
            reader.onload = (e2) => {
                this.imageDataUrl = e2.target.result;
            };
        },
        proceed(e){
            e.preventDefault();
            const config = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: (progressEvent) => {
                    this.percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                }
            }

            let postData =  new FormData();
            postData.append('image', this.image);
            postData.append('company', this.company_name);
            postData.append('format', this.format);


            // const postData = {
            //     company: this.company_name,
            //     image: this.image,
            //     format: this.format,
            // }
            console.log(postData);
            axios.post('/upload-image', postData, config)
            .then((res) => {
                console.log(res);
                console.log(res.data);
            }).catch((err) => {
                console.log(err.response.data);
            })

        }
    },
    
}
</script>

<style>

</style>