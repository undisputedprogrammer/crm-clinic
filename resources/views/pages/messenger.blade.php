<x-easyadmin::app-layout>
    <div>
        <div x-data="{

            convertTime(timestamp){
                let date = new Date(timestamp);

                let today = new Date();

                let hours = date.getHours();
                let amOrpm = 'AM'
                if(hours > 12){
                    amOrpm = 'PM';
                    hours = hours % 12 || 12;
                }
                minutes = date.getMinutes();

                if(date.getDate() == today.getDate() && date.getMonth() == today.getMonth() && date.getFullYear() == today.getFullYear())
                {
                    return `${hours}:${minutes.toLocaleString('en-US', { minimumIntegerDigits: 2 })} ${amOrpm}`;
                }else{
                    let day = date.getDate();
                    let month = date.toLocaleString('en-US',{month:'short'});
                    return `${month} ${day} ${hours}:${minutes.toLocaleString('en-US', { minimumIntegerDigits: 2 })} ${amOrpm}`;
                }
            }
        }" class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black "
        x-init="
        console.log('{{$latest->id}}');
        if(latest == null){
            latest = {{$latest->id}};
        }">

            <!-- Header -->
            <x-display.header />
            <x-sections.side-drawer />
            {{-- page body --}}



            <div x-data="{


                selected: null,
                lead : null,
                chats: [],
                loadingChats: false,
                showChat(lead) {
                    this.lead = lead;
                    console.log('loading chats');
                    this.loadingChats = true;
                    this.selected = lead.id;
                    setTimeout(() => {
                        console.log(lead);
                        this.chats = allChats[lead.id];
                        console.log(this.chats);

                        this.loadingChats = false;
                      }, '1000');

                }
            }"
            @appendChat.window="console.log('event captured');"

             class="h-[calc(100vh-3.5rem)] bg-base-200 w-full flex justify-evenly">

                {{-- body starts here --}}

                <div class="  overflow-auto basis-[20%] my-2">

                    @foreach ($leads as $lead)
                        <div @click.prevent.stop="

                        showChat({{ $lead }});"
                            class=" flex items-start  cursor-pointer hover:bg-base-100 rounded-xl my-1.5 w-full"
                            :class = "selected == '{{$lead->id}}' ? ' bg-base-100' : '' "
                            x-init = "allChats[{{$lead->id}}] = {{$lead->chats}}">

                            <div x-data="{
                                length : 0,
                                message : ''
                            }" class=" px-3 text-base-content py-4 w-full" x-init="length = allChats[{{$lead->id}}].length;">
                                <div class="flex items-bottom justify-between w-full">
                                    <p class=" text-base font-medium">
                                        {{ $lead->name }}
                                    </p>
                                    <p class="text-xs" x-text="convertTime(allChats[{{$lead->id}}][allChats[{{$lead->id}}].length -1].created_at)">

                                    </p>
                                </div>
                                <p  class=" mt-1 text-sm line-clamp-1" x-text=" allChats[{{$lead->id}}][allChats[{{$lead->id}}].length - 1].message" >

                                </p>
                            </div>
                        </div>
                    @endforeach

                </div>


                {{-- Messages section --}}


                <div @displayChats="console.log($event.detail);"
                    class="flex justify-evenly basis-[76%] rounded-xl my-2  overflow-hidden"
                    style="background-color: #DAD3CC">

                    <div class="py-2 px-3 basis-[65%]   hide-scroll relative flex">


                            <p x-show="lead == null" class="font-semibold text-lg text-center py-2.5 w-full" x-text="lead == null ? 'Select a lead' : '' "></p>


                        <template x-if="chats.length > 0">
                            <div class="overflow-auto w-full hide-scroll mb-[72px]">
                                <template x-for="chat in chats">
                                    <div class="chat " :class="chat.direction == 'Inbound' ? ' chat-start' : ' chat-end'">
                                        <div class="chat-bubble text-base-content font-medium" x-text="chat.message"
                                            :class="chat.direction == 'Inbound' ? ' bg-base-100' : ' bg-[#128c7e] text-white'">
                                        </div>
                                    </div>
                                </template>
                            </div>

                        </template>

                        <template x-if="lead != null">
                            <x-forms.whatsapp-form :templates="$templates"/>
                        </template>


                    </div>

                    {{-- loading animation --}}
                    <div x-show="loadingChats" class=" h-screen w-full flex justify-center items-center bg-black absolute z-40 top-0 left-0 opacity-40">

                        <span class="loading loading-ball loading-xs bg-primary"></span>
                        <span class="loading loading-ball loading-sm bg-primary"></span>
                        <span class="loading loading-ball loading-md bg-primary"></span>
                        <span class="loading loading-ball loading-lg bg-primary"></span>

                    </div>

                    <div class="basis-[30%] rounded-xl bg-base-100 my-2 text-base-content">
                        <h1 class=" font-semibold text-center text-lg w-full py-2 text-primary" x-text=" lead != null ? 'Lead details' : 'Select a lead' ">Lead details</h1>

                        <template x-if="lead != null && !loadingChats">
                            <div class=" flex flex-col space-y-2 p-3 transition-all">

                                <p class=" font-medium text-base">Name : <span x-text="lead.name"></span></p>

                                <p class=" font-medium text-base">City : <span x-text="lead.city"></span></p>

                                <p class=" font-medium text-base">Phone : <span x-text="lead.phone"></span></p>

                                <p class=" font-medium text-base">Email : <span x-text="lead.email"></span></p>

                            </div>
                        </template>
                    </div>



                </div>

                {{-- body ends here --}}
            </div>

        </div>

    </div>
</x-easyadmin::app-layout>
