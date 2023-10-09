<div x-show= "selected_action == 'Add Followup'">
    <form x-show="fp.next_followup_date == null" x-transition
                            x-data ="
                            { doSubmit() {
                                let form = document.getElementById('next-followup-form');
                                let formdata = new FormData(form);
                                formdata.append('followup_id',fp.id);
                                formdata.append('lead_id',fp.lead.id);
                                if(fp.converted){
                                    formdata.append('converted',fp.converted);
                                    console.log(fp.converted);
                                }

                                $dispatch('formsubmit',{url:'{{route('next-followup')}}', route: 'next-followup',fragment: 'page-content', formData: formdata, target: 'next-followup-form'});
                            }}"
                            @submit.prevent.stop="doSubmit();"

                            @formresponse.window="
                            if ($event.detail.target == $el.id) {
                                if ($event.detail.content.success) {
                                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                    $el.reset();
                                    $dispatch('linkaction',{link:'{{route('followups')}}',route:'followups', fragment:'page-content', fresh: true});
                                    if($event.detail.content.followup != null && $event.detail.content.followup != undefined)
                                    {

                                    fp.next_followup_date = $event.detail.content.followup.next_followup_date;
                                    fp.actual_date = $event.detail.content.followup.actual_date;

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
                            id="next-followup-form" action="" class=" mt-5">

                                <div>
                                    <label x-show="fp.next_followup_date == null" for="next-followup-date" class="text-sm font-medium">Schedule date for next follow up</label>

                                    <input x-show="fp.next_followup_date == null" id="next-followup-date" name="next_followup_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full">
                                </div>

                                <button :disabled=" fp.remarks && fp.remarks.length == 0 ? true : false" class=" btn btn-xs btn-primary mt-2" type="submit">Schedule next follow up</button>

                            </form>
</div>
