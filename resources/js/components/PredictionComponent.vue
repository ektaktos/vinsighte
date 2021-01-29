<template>
  <div class="relative flex items-top justify-center py-5">
        <div class="card shadow p-2 prediction-card col-lg-6 col-md-6">
            <div class="card-body">
            <h3 class="text-center">Prediction</h3>
            <fieldset>
                <legend class="w-auto"><h5>Images</h5></legend>
                <form @submit="proceed">
                <div class="form-group form-inline">
                    <label class="col-sm-2 text-left p-0">Company</label>
                    <input type="text" class="form-control col-sm-8" v-model="user.name">
                </div>

                <div class="form-group form-inline">
                    <label class="col-sm-2 text-left p-0">Image</label>
                    <input type="file" ref="file" id="file-upload" multiple accept="image/*, application/pdf" @change="uploadImage">
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
                    <!-- <div v-for="(image, index) in imageDataUrl" :key="index"> -->
                        <img v-for="(image, index) in imageDataUrl" :key="index" :src="image" class="imagePreview">
                    <!-- </div> -->
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="w-50">
                        <progress max="100" :value.prop="percentCompleted"></progress>
                    </div>
                    <div class="w-50 text-right">
                        <button class="btn btn-primary" type="submit">Start Extraction<span class="spin" v-if="isLoading"></span></button>
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
    props: ['user'],
    data() {
        return {
            images: [],
            image: '',
            imageDataUrl: [],
            format: '',
            isLoading: false,
            percentCompleted: 0,
        }
    },
    mounted(){
        
    },
    methods: {
        uploadImage(e) {
            const tempImages = this.$refs.file.files;
            for (let i = 0; i < tempImages.length; i++) {
                const element = tempImages[i];
                this.images.push(element); 
            }
            this.image = this.$refs.file.files[0];
            // console.log(this.images);
            // console.log(this.image);
            const images = e.target.files;
            for (let i = 0; i < images.length; i++) {
                const reader = new FileReader();
                reader.readAsDataURL(images[i]);
                reader.onload = (e2) => {
                    this.imageDataUrl.push(e2.target.result);
                };
            }
        },
        proceed(e){
            e.preventDefault();
            this.isLoading = true
            const config = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: (progressEvent) => {
                    this.percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                }
            }
            let postData =  new FormData();
            for (let i = 0; i < this.images.length; i++) {
                const element = this.images[i];
                postData.append(`images${i}`, element);
            }
            postData.append('format', this.format);
            axios.post('/upload-image', postData, config)
            .then((res) => {
                this.isLoading = false;
                this.$swal('Success!!','Your Input has been Successfully processed', 'success').then(() => {
                    window.location.href = 'http://vinsighte.herokuapp.com/logs';
                });
            }).catch((err) => {
                this.isLoading = false;
                console.log(err.response.data);
            })
        }
    },
    
}
</script>

<style scoped>
    .spin {
    display: inline-block;
    width: 15px;
    height: 15px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
    -webkit-animation: spin 1s ease-in-out infinite;
    margin-left: 10px;
  }
  @keyframes spin {
      to { -webkit-transform: rotate(360deg); }
  }
  @-webkit-keyframes spin {
      to { -webkit-transform: rotate(360deg); }
  }
</style>