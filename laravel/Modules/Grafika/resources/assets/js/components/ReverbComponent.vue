import {onMounted, onUnmounted} from "vue";

#...Component code

onMounted(() => {
   const user = usePage().props.auth.user

   window.Echo.private(`App.Models.User.${user.id}`)
        .listen('ReverbExampleEvent', (e:any) => {
           alert('Reverb 🚀')
     })

});