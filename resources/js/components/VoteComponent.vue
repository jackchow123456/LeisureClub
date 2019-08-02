<template>
    <div class="container">
        <div class="ge">
            <a @click="vote(1)">1</a>
            <p>{{value1}} 票</p>
        </div>
        <div class="ge">
            <a @click="vote(2)">2</a>
            <p>{{value2}} 票</p>
        </div>
    </div>
</template>

<script>
    import server from '../server';

    export default {
        data() {
            return {
                value1: 0,
                value2: 0,
                path: "ws://127.0.0.1:1216",
                socket: ""
            }
        },
        mounted() {
            this.init()
        },
        methods: {
            init: function () {
                if (typeof (WebSocket) === "undefined") {
                    alert("您的浏览器不支持socket")
                } else {
                    // 实例化socket
                    this.socket = new WebSocket(this.path)
                    // 监听socket连接
                    this.socket.onopen = this.open
                    // 监听socket错误信息
                    this.socket.onerror = this.error
                    // 监听socket消息
                    this.socket.onmessage = this.getMessage
                }
            },
            vote: function ($id) {
                server.Vote($id).then(function (response) {
                    var $data = response.data
                    console.log($data)

                }).catch(function (error) {
                    console.log(error)
                });
            },
            open: function () {
                console.log("socket连接成功")
                let str = '["vote","{\\"id\\":1,\\"num\\":123}"]';
                console.log(JSON.parse(str));
            },
            error: function () {
                console.log("连接错误")
            },
            getMessage: function (msg) {
                console.log("收到服务器信息" + msg.data)
                let index = msg.data.indexOf('[');
                if (index > 0) {
                    try{
                        let real_data = msg.data.substr(index);
                        let data = JSON.parse(real_data);
                        if (data[0] == 'vote') {
                            let result = JSON.parse(data[1]);
                            let valueName = 'value' + result.id;
                            this[valueName] = result.num;
                        }
                    } catch(e) {
                        // 忽略报错
                    }

                }

            },
            send: function (params) {
                let data = {};
                this.socket.send(params)
            },
            close: function () {
                console.log("socket已经关闭")
            }
        }
    }
</script>
<style scoped>
    .ge {
        width: 100px;
        display: inline-block;
    }
</style>