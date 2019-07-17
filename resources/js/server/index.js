import axios from "axios";

const instance = axios.create({
    baseURL: 'http://www.framework_oa.com/api/',
    headers: {
        'X-Custom-Header': 'foobar',
        'Authorization': 'bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cuZnJhbWV3b3JrX29hLmNvbVwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTU2MDA0OTk1MCwiZXhwIjoxNTY1MjMzOTUwLCJuYmYiOjE1NjAwNDk5NTAsImp0aSI6IkJlT2t3UXh6SUgzMnk5SDQiLCJzdWIiOjEyMDA1LCJ1c2VyX2lkIjoxMjAwNX0.MJoNR_Xia9vuEx02TrmCAdAtdJdIhMznAlPTHHc3jQM'
    }
});



export default {
    // 七牛云上传文件示例
    QiNiuUpload: ($data) => instance.post('entry/application/upload',
        $data,
        {headers: {'Content-Type': 'multipart/form-data'}}
    )
}
