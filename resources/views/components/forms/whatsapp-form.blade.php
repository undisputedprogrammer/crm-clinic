@props(['templates'])
<div class=" absolute bottom-0 w-full pt-1 z-40">
    <form x-data="{
        value : '',
        custom : false,
        validate(){
            if(this.value == 'custom'){
                this.custom = true;
            }
        },
        doSubmit(){
            let form = document.getElementById('wp-message-form');
            let formdata = new FormData(form);
            formdata.append('lead_id',lead.id);
            $dispatch('formsubmit',{url:'{{route('message.sent')}}', route: 'message.sent',fragment: 'page-content', formData: formdata, target: 'wp-message-form'});
        },


    }"
    @formresponse.window="
    if($el.id == $event.detail.target){
        console.log($event.detail.content.message);

        if($event.detail.content.status == 'success'){
            chats.push($event.detail.content.chat);
            allChats[lead.id].push($event.detail.content.chat);
            $dispatch('showtoast', {message: 'Message sent successfully', mode: 'success'});
            $el.reset();
        }
        else if ($event.detail.content.status == 'fail') {
            $dispatch('showtoast', {message: $event.detail.content.errors, mode: 'error'});

        }
        else{
            $dispatch('formerrors', {errors: $event.detail.content.errors});
        }

    }"
     class="flex justify-center space-x-3 py-3" id="wp-message-form" action="{{route('message.sent')}}" method="POST" @submit.prevent.stop="doSubmit()">
    @csrf

        <select @change.prevent.stop="validate()" :required="!custom ? true : false " x-model="value" x-show="!custom" name="template" id="select-template" class=" select select-info bg-white w-[78%] lg:w-[80%] focus:ring-0 focus:outline-none" >
            <option value="" disabled selected>-- Select template  --</option>
            @foreach ($templates as $template)
                <option value="{{$template->id}}">{{$template->template}}</option>
            @endforeach
            <option value="custom">Custom message</option>

        </select>

        <button @click.prevent.stop="custom = false;
        value = ''" x-show="custom" class="btn text-primary">
            T
        </button>

        <input x-show="custom" name="message" :required="custom ? true : false " type="text" placeholder="Type message and press sent" class="input input-info bg-white w-[74%]  focus:ring-0 focus:outline-none text-black font-medium">

        <button type="submit" class="btn btn-success">Send</button>
    </form>
</div>
