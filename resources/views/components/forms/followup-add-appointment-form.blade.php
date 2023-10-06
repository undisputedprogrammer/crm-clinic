@props(['doctors'])
{{-- schedule appointment form --}}
<div x-show="selected_action == 'Schedule Appointment'">
<template x-if="fp.next_followup_date != null">
    <p class=" text-primary font-medium py-4">Next followup is scheduled for this lead.</p>
</template>

<template x-if="lead.status == 'Converted' ">
    <p class=" text-primary font-medium py-4">Appointment is already scheduled for this lead</p>
</template>

<template x-if="lead.status == 'Closed' ">
    <p class=" text-error text-base font-medium py-4">This lead is closed!</p>
</template>

<form x-show="fp.converted != true && fp.next_followup_date == null" x-cloak x-transition
                        x-data ="
                        { doSubmit() {
                            let form = document.getElementById('appointment-form');
                            let formdata = new FormData(form);
                            formdata.append('followup_id',fp.id);
                            formdata.append('lead_id',fp.lead.id);
                            $dispatch('formsubmit',{url:'{{route('add-appointment')}}', route: 'add-appointment',fragment: 'page-content', formData: formdata, target: 'appointment-form'});
                        }}"
                        @submit.prevent.stop="doSubmit();"

                        @formresponse.window="
                        if ($event.detail.target == $el.id) {
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                $el.reset();

                                if($event.detail.content.followup != null && $event.detail.content.followup != undefined)
                                {

                                fp.lead.status = $event.detail.content.lead.status;
                                fp.actual_date = $event.detail.content.followup.actual_date;
                                fp.converted = $event.detail.content.followup.converted;

                                }

                                if($event.detail.content.appointment != null && $event.detail.content.appointment != undefined){
                                    lead.appointment = $event.detail.content.appointment;
                                    fp.lead.appointment = $event.detail.content.appointment;
                                }

                                if($event.detail.content.followup_remark != null || $event.detail.content.followup_remark != undefined)
                                {
                                    fp.remarks.push($event.detail.content.followup_remark);

                                }

                                historyLoading = true;
                                axios.get('/api/followup',{
                                    params: {
                                    id: fp.id,
                                    lead_id: fp.lead.id

                                    }
                                  }).then(function (response) {
                                    history = response.data.followup;
                                    console.log(response.data.followup);
                                    historyLoading = false;

                                  }).catch(function (error){
                                    console.log(error);
                                    historyLoading = false;
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
                         x-show="lead.status != 'Converted' && fp.next_followup_date == null" action="" class=" mt-1.5">

                            <div>
                                <label x-show="fp.next_followup_date == null && fp.converted == null" for="next-followup-date" class="text-sm font-medium text-secondary mb-1">Schedule appointment</label>

                                <select class="select select-bordered w-full bg-base-200 text-base-content" name="doctor">
                                    <option disabled>Choose Doctor</option>
                                    @foreach ($doctors as $doctor)
                                    <template x-if="lead.center_id == '{{$doctor->center_id}}' ">
                                            <option value="{{$doctor->id}}">{{$doctor->name}}</option>
                                    </template>
                                    @endforeach

                                </select>

                                <input x-show="fp.next_followup_date == null && fp.converted == null" id="next-followup-date" name="appointment_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full mt-1.5">
                            </div>

                            <button :disabled=" fp.converted == true ? true : false" class=" btn btn-xs btn-primary mt-2" type="submit">Schedule appointment</button>

                        </form>


                        {{-- *************************************************************************
                        If appointment is already scheduled.., the below portion will be shown
                        ************************************************************* --}}

                        {{-- mark consulted form --}}


                        <div x-show="fp.consulted != null" class="mt-4">
                            <p class=" text-success font-medium">Consult completed on <span x-text="lead.appointment != null ? lead.appointment.appointment_date : '' "></span></p>
                            <label @click.prevent.stop="showconsultform = true" class=" text-base-content font-medium mt-1" x-text="lead.appointment != null && lead.appointment.remarks != null ? lead.appointment.remarks : 'No remark made' "></label>
                        </div>
</div>
