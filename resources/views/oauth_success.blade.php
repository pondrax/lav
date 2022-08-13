<script>
  localStorage.setItem('access_token','{{$access_token}}');
  localStorage.setItem('access_user', '@json($user)')
  setTimeout(()=>{
    location.href = '/app'
  },3000)
</script>

<h3>{{$message}}</h3>