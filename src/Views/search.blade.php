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
		<div class="col-md-8 mx-auto">
			<form @submit.prevent="doSearch">
				<div class="form-group">
					<label for="exampleInputEmail1">Search</label>
					<input type="Search" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Search" v-model="search">
				</div>
				<div class="form-group">
					<label for="exampleInputEmail1">From</label>
					<input type="Search" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="2023-08-07" v-model="from" required>
				</div>
				<div class="form-group">
					<label for="exampleInputEmail1">To</label>
					<input type="Search" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="2023-08-07" v-model="to" required>
				</div>
				<div class="mb-3 form-check">
					<input type="checkbox" class="form-check-input" id="show_response">
					<label class="form-check-label" for="show_response">Show response</label>
				</div>
				<div class="mb-3 form-check">
					<input type="checkbox" class="form-check-input" id="show_response">
					<label class="form-check-label" for="show_response">Paginate</label>
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.25.0/axios.min.js"></script>
<script>
    new Vue({
        el: '#constApp',
        data: {
			search: '',
			show_response: false,
			paginate: false,
			from: '',
			to: '',
        },
        methods: {
			doSearch(){
				window.location.replace(`/log-viewer/show/${this.from}/${this.to}?search=${this.search}&paginate=${this.paginate}&show_response=${this.show_response}`)
			}
        },
		mounted(){
			var date = new Date();
			var dd = String(date.getDate()).padStart(2, '0');
			var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
			var yyyy = date.getFullYear();
			this.to = yyyy + '-' + mm + '-' + dd;

			const tenDaysAgo = new Date(date);
			tenDaysAgo.setDate(date.getDate() - 10);
			const year = tenDaysAgo.getFullYear();
			const month = String(tenDaysAgo.getMonth() + 1).padStart(2, '0');
			const day = String(tenDaysAgo.getDate()).padStart(2, '0');

			this.from = year + '-' + month + '-' + day;
		}
    })
</script>


</body>
</html>
