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
<style>
    span{
        margin-right: 50px;
    }
    .card{
        text-align: left;
    }
</style>
<body>
<div class="m-3" id="constApp">

    <div class="row">
        <div class="col-md-12 mx-auto text-center">

            <div class="col-12 text-danger text-left">
                <b>@{{path}}</b>
            </div>
            <br>

            <form class="form-inline">
                <label class="sr-only" for="inlineFormInputName2">search</label>
                <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="search" v-model="search">

                <label class="sr-only" for="inlineFormInputName2">time</label>
                <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="12:00:03  /  12:00" v-model="time">

                <div class="form-check mb-2 mr-sm-2">
                    <input class="form-check-input" type="checkbox" id="inlineFormCheck" v-model="showResponse">
                    <label class="form-check-label" for="inlineFormCheck">
                        show response
                    </label>
                </div>
                <div class="form-check mb-2 mr-sm-2">
                    <input class="form-check-input" type="checkbox" id="inlineFormCheck2" v-model="justErrors">
                    <label class="form-check-label" for="inlineFormCheck2">
                        just errors
                    </label>
                </div>
                <div class="form-check mb-2 mr-sm-2">
                    <input class="form-check-input" type="checkbox" id="inlineFormCheck3" v-model="noPaginate">
                    <label class="form-check-label" for="inlineFormCheck3">
                        no paginate
                    </label>
                </div>
                <div class="form-check mb-2 mr-sm-2">
                    <input class="form-check-input" type="checkbox" id="inlineFormCheck4" v-model="sortDuration">
                    <label class="form-check-label" for="inlineFormCheck4">
                        sort duration
                    </label>
                </div>

                <button type="submit" class="btn btn-primary mb-2" @click.prevent="getData(true)">Search</button>
            </form>


            <div  v-for="(req,i) in requests" :id="'accordion'+i" style="margin-bottom: 10px">

                <div class="card" >
                    <div :class="'card-header  '+ (req.error==true?'alert alert-danger':'')">
                        <a class="card-link" data-toggle="collapse" :href="'#collapse'+i">
                            <span>time: @{{req.time}}</span>
                            <span>ip: @{{req.ip}}</span>
                            <span>duration: @{{req.duration}}</span>
                            <br>
                            <span>url: @{{req.url}}</span>
                            <br>
                            <span>request: @{{req.request}}</span>
                            <br>
                            <span>token: @{{req.token}}</span>
                            <br>
                            <span @click="seeFiles(req.filename)">filename: @{{req.filename}}</span>
                        </a>
                    </div>
                    <div :id="'collapse'+i" :class="'collapse '+ (req.response!=undefined?'show':'')" :data-parent="'#accordion'+i">
                        <div class="card-body">
                            <span>response: @{{req.response}}</span>
                        </div>
                    </div>
                </div>
            </div>

                        {{--<tr >--}}
                            {{--<td style="white-space: break-spaces; text-align: left">@{{req}}</td>--}}
                        {{--</tr>--}}


            <div class="form-group" style="margin: 0 10px" v-if="requests.length==0 && isLoadeing==false">
                <div :class="['alert','alert-danger','alert-dismissible','bg-danger-active']">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <span>No item found!</span>
                </div>
            </div>

            <button v-if="isLoadeing==false" class="btn btn-primary" @click.prevent="getData()">Load more</button>
            <button v-else class="btn btn-primary" disabled>
                <span class="spinner-border spinner-border-sm"></span>
                Loading..
            </button>

        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.25.0/axios.min.js"></script>
<script>
    new Vue({
        el: '#constApp',
        data: {
            requests : [],
            path : '{{$fullPath}}',

            search : '',
            time:'',
            showResponse:false,
            justErrors:false,
            noPaginate:false,
            sortDuration:false,

            isLoadeing : false,
        },
        methods: {


            getData(reset=false){
                if(reset)
                    this.requests=[];

                this.isLoadeing=true;
                axios.get('/log-viewer/show-ajax',{
                    params:{
                        'path':this.path,
                        'search':this.search,
                        'time':this.time,
                        'show_response':this.showResponse,
                        'just_errors':this.justErrors,
                        'sort_duration':this.sortDuration,
                        'no_paginate':this.noPaginate,
                        'loaded_count':this.requests.length,
                    }
                })
                .then(res=>{
                    this.isLoadeing=false;
                    this.requests=this.requests.concat(res.data.requests)
                    this.path = res.data.fullPath

                })
            },

            seeFiles(filename){
                filename =  filename.split('/')
                window.location.replace(`/log-viewer/show/${filename[filename.length - 2]}/${filename[filename.length - 1]}`)
            }
        },
        mounted(){
            const urlParams = new URLSearchParams(window.location.search);
            const search = urlParams.get('search');
            if(search != null && search !== ''){
                this.search = search
            }

            const show_response = urlParams.get('show_response');
            if(show_response != null && show_response !== ''){
                this.showResponse = show_response == 1? true: false
            }

            const paginate = urlParams.get('paginate');
            if(paginate != null && paginate !== ''){
                this.noPaginate = paginate == 1? false: true
            }

            this.getData()
        },
        watch: {

        },

    })
</script>


</body>
</html>





