<script>
  setTimeout(()=>{
    location.href = '{{url("/api/auth/$provider")}}'
  },3000)
</script>

<h3>{{$message}}</h3>