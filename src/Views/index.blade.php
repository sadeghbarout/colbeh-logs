<!DOCTYPE html>
<html lang="en">
<head>
    <title>Logs helper</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>

</head>
<body>
<div class="container mb-5" id="constApp">

    <div class="row">
        <div class="col-md-8 mx-auto text-center">


            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr >
                            <th colspan="3"  >
                                <button v-if="path != '' " @click="getFiles('')" class="btn btn-outline-info btn-sm float-left">Back</button>
                                <span v-else  class="text-danger">Folders</span>
                                <span class="text-danger" v-html="path"></span>
                            </th>
                        </tr>
                        <tr>
                            <th>File</th>
                            <th>Size</th>
                            <th>Operation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(file, index) in files">
                            <td>@{{file.name}}</td>
                            <td :class="'alert alert-'+(getClass(file.size))">@{{file.size_text}}</td>
                            <td>
                                <button @click="getFiles(file)" class="btn btn-outline-primary btn-sm" v-if="file.is_dir == true">Files</button>
                                <a target="_blank" :href="'log-helper/show/'+path+'/'+file.name" class="btn btn-outline-primary btn-sm" v-if="file.is_dir == false ">View</a>
                                <button @click="deleteFolder(file, index)" class="btn btn-outline-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.25.0/axios.min.js"></script>
<script>
    new Vue({
        el: '#constApp',
        data: {
                data : [],
            files : [],
            maxSize : 0,
            path : '',
        },
        methods: {
            getClass(fileSize){
                $p = parseInt((100*fileSize)/this.maxSize);
                if($p < 30)
                    $class= "success";
                else if($p<60)
                    $class= "warning";
                else
                    $class= "danger";

                return $class;
            },


            deleteFolder(folder, index){
                var res = confirm("are u sure")
                if(res == false)
                    return;

                axios.delete('/log-helper/delete',{
                    params:{
                        'path':this.path,
                        'name':folder.name,
                    }
                })
                .then(res=>{
                    if(res.data.result == "success"){
                        this.files.splice(index, 1)
                    }
                })
            },


            getFiles(file){
                axios.get('/log-helper/file',{
                    params:{
                        'path':file.name,
                    }
                })
                .then(res=>{
                    this.files = res.data.files
                    this.maxSize = res.data.maxSize
                    this.path = res.data.path

                })
            },

        },
        mounted(){
            this.getFiles('')
        },
        watch: {

        },

    })
</script>


</body>
</html>





