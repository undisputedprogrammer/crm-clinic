<x-easyadmin::app-layout>
    <div>
        <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

            <!-- Header -->
            <x-display.header />
            <!-- ./Header -->



            <div class=" min-h-[calc(100vh-3.5rem)] pb-[2.8rem] w-full mx-auto  bg-base-100 ">

                <div class="w-[96%] mx-auto rounded-xl bg-base-100 p-3 my-4">
                    <div class="flex space-x-3 justify-evenly items-center">

                        <div
                            class="flex flex-col space-y-1 bg-base-200 w-1/4 h-16 rounded-xl justify-center items-center ">
                            <label for=""
                                class=" font-medium text-primary w-[90%] flex justify-between items-center">
                                <span>Total leads this month</span>
                                <span class="text-lg font-semibold text-secondary">{{ $lpm }}</span>
                            </label>
                            {{-- <progress class="progress progress-success w-[90%] mx-auto" value="50" max="100"></progress> --}}
                        </div>

                        <div class="flex flex-col space-y-1 bg-base-200 w-1/4 rounded-xl items-center py-4">
                            <label for="" class=" font-medium text-primary w-[90%] flex justify-between">
                                <span>Lead followed up this month</span>
                                <span
                                    class=" text-base font-semibold text-secondary">{{ $ftm }}/{{ $lpm }}</span>
                            </label>
                            @php
                                if ($lpm != 0) {
                                    $perc_lf = ($ftm / $lpm) * 100;
                                } else {
                                    $perc_lf = 0;
                                }

                            @endphp
                            <progress class="progress progress-success w-[90%] mx-auto" value="{{ $perc_lf }}"
                                max="100"></progress>

                        </div>

                        <div class="flex flex-col space-y-1 bg-base-200 w-1/4 rounded-xl items-center py-4">
                            <label for="" class=" font-medium text-primary w-[90%] flex justify-between">
                                <span>Leads converted this month</span>
                                @php
                                    if ($lpm != 0) {
                                        $ctm = $lcm / $lpm;
                                    } else {
                                        $ctm = 0;
                                    }
                                @endphp
                                <span
                                    class="text-base font-semibold text-secondary">{{ $lcm }}/{{ $lpm }}</span>
                            </label>

                            <progress class="progress progress-success w-[90%] mx-auto" value="{{ $ctm * 100 }}"
                                max="100"></progress>
                        </div>

                        <div
                            class="flex flex-col space-y-1 bg-base-200 justify-center h-16 w-1/4 rounded-xl items-center py-4">
                            <label for=""
                                class=" font-medium text-primary w-[90%] flex justify-between items-center">
                                <span>Total scheduled follow ups pending</span>
                                <span class="text-lg font-semibold text-secondary">{{ $pf }}</span>
                            </label>

                        </div>

                    </div>



                    {{-- import leads form --}}
                    @can('import-lead')
                        <div class="my-3 bg-base-200 p-3 rounded-xl w-fit">
                            <h1 class="font-semibold mb-1 text-primary">Import leads from Excel</h1>
                            <form x-data="{ doSubmit() {
                    let form = document.getElementById('import-form');
                    let formdata = new FormData(form);

                    $dispatch('formsubmit',{url:'{{ route('import-leads') }}', route: 'import-leads',fragment: 'page-content', formData: formdata, target: 'import-form'});
                }}" @submit.prevent.stop="doSubmit();"
                                @formresponse.window="
                console.log($event.detail.content);
                if ($event.detail.target == $el.id) {
                    if ($event.detail.content.success) {
                            $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});

                            $dispatch('formerrors', {errors: []});
                        } else if (typeof $event.detail.content.errors != undefined) {
                            $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                        } else{
                            $dispatch('formerrors', {errors: $event.detail.content.errors});
                        }
                }"
                                id="import-form" class="flex space-x-3">
                                <input type="file" name="sheet"
                                    class="file-input file-input-bordered file-input-success text-base-content file-input-sm w-full max-w-xs" />
                                <button type="submit" class="btn btn-sm btn-success">Import</button>
                            </form>
                        </div>
                    @endcan

                    @can('is-admin')
                        <div class="bg-base-200 rounded-xl my-3 p-3 w-fit">
                            <h2 class="text-primary font-medium">More actions</h2>

                            <div class=" flex space-x-2 mt-1">
                                <button class="btn btn-sm btn-secondary"
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('manage-questions') }}',
                                        route:'manage-questions',
                                        fragment:'page-content'
                                        })">Manage Questions
                                </button>

                                <button class="btn btn-sm btn-secondary"
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('appointments.index') }}',
                                        route:'appointments.index',
                                        fragment:'page-content'
                                    })">Manage Appointments
                                </button>

                                <button class="btn btn-sm btn-secondary"
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('doctors.index') }}',
                                        route:'doctors.index',
                                        fragment:'page-content'
                                    })">Manage Doctors
                                </button>

                                <button class="btn btn-sm btn-secondary"
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('messages.index') }}',
                                        route:'messages.index',
                                        fragment:'page-content'
                                    })">Manage Messages
                                </button>

                                <button class="btn btn-sm btn-secondary"
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('agents.index') }}',
                                        route:'agents.index',
                                        fragment:'page-content'
                                    })">Manage Agents
                                </button>

                            </div>
                        </div>
                    @endcan

                </div>


            </div>

        </div>
    </div>
<x-footer/>
</x-easyadmin::app-layout>
