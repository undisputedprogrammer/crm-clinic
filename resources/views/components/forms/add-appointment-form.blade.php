@props(['doctors'])
{{-- schedule appointment form --}}
<form x-show="convert && lead.status != 'Converted'" x-cloak x-transition
x-data ="
{ doSubmit() {
    let form = document.getElementById('appointment-form');
    let formdata = new FormData(form);
    formdata.append('no_followup',true);
    formdata.append('lead_id',lead.id);
    $dispatch('formsubmit',{url:'{{route('add-appointment')}}', route: 'add-appointment',fragment: 'page-content', formData: formdata, target: 'appointment-form'});
}}"
@submit.prevent.stop="doSubmit();"

@formresponse.window="
if ($event.detail.target == $el.id) {
    if ($event.detail.content.success) {
        $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
        $el.reset();

        if($event.detail.content.lead != null && $event.detail.content.lead != undefined)
        {

        lead.status = $event.detail.content.lead.status;


        }

        axios.get('/api/get/remarks',{
            params: {
            remarkable_id: lead.id,
            remarkable_type: 'App\Models\Lead'
            }
          }).then(function (response) {

            remarks = response.data.remarks;
            leads[lead.id].remarks = remarks;
            document.getElementById('add-remark-form').reset();

          }).catch(function (error){
            console.log(error);
          });

          $dispatch('formerrors', {errors: []});
    }

    else if (typeof $event.detail.content.errors != undefined) {
        $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

    } else{
        $dispatch('formerrors', {errors: $event.detail.content.errors});
    }
}
"
id="appointment-form"
 x-show="lead.status != 'Converted' && lead.followup_created == false" action="" class=" mt-1.5">

    <div>
        <label  for="next-followup-date" class="text-sm font-medium text-secondary mb-1">Schedule appointment</label>

        <select class="select select-bordered w-full max-w-sm bg-base-200 text-base-content" name="doctor">
            <option disabled>Choose Doctor</option>
            @foreach ($doctors as $doctor)
                <option value="{{$doctor->id}}">{{$doctor->name}}</option>
            @endforeach

        </select>

        <input  id="next-followup-date" name="appointment_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full mt-1.5 max-w-sm">
    </div>

    <button :disabled=" lead.status == 'Converted' ? true : false" class=" btn btn-xs btn-primary mt-2" type="submit">Schedule appointment</button>

</form>
