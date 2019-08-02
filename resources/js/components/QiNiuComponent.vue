<template>
    <div class="container">
        <div class="form-group">
            <input type="file" @change="getFile($event)">
        </div>
        <div class="form-group">
            <input type="submit" @click="upload()">
        </div>
        <p style="color: green" v-if="success"> {{ success }} </p>
        <p style="color: red" v-if="error"> {{ error }} </p>
        <img style="width: 100px" v-if="url" :src="url" />

    </div>
</template>

<script>
    import server from '../server';

    export default {
        data() {
            return {
                url: '',
                success: '',
                error: ''
            }
        },
        mounted() {
        },
        methods: {
            upload: function () {
                event.preventDefault();// 取消默认行为
                let formData = new FormData();
                formData.append('photo', this.file);
                var that = this;
                server.QiNiuUpload(formData).then(function (response) {
                    var $data = response.data
                    if ($data.code == 200) {
                        that.url = $data.data.base_url + $data.data.key;
                        that.success = '上传成功！';
                    }
                }).catch(function (error) {
                    that.error = '上传失败,图片不符合上传规格,(不能超过2M)'+error;
                });
            },

            getFile(event) {
                this.file = event.target.files[0];
            }
        }
    }
</script>
