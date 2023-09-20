<!DOCTYPE html>
<html x-data="{theme: $persist('newdark'), href: '', currentpath: '{{url()->current()}}', currentroute: '{{ Route::currentRouteName() }}', compact: $persist(false)}"
@themechange.window="theme = $event.detail.darktheme ? 'newdark' : 'light';" lang="{{ str_replace('_', '-', app()->getLocale()) }}"
x-init="window.landingUrl = '{{url()->full()}}'; window.landingRoute = '{{ Route::currentRouteName() }}'; window.renderedpanel = 'pagecontent';"
@pagechanged.window="
currentpath=$event.detail.currentpath;
currentroute=$event.detail.currentroute;"
@routechange.window="currentroute=$event.detail.route;"
:data-theme="theme">
    <head>
        <title>{{ config('app.name', 'CRAFT Hospital and Research Centre') }}</title>
        <link rel="shortcut icon" type="image/jpg" href="{{asset('favicon-craft.ico')}}"/>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">



        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('css')
        @stack('header_js')
    </head>
    <body x-data="initPage" x-init="initAction();"
        @linkaction.window="initialised = false; fetchLink($event.detail);"
        @formsubmit.window="postForm($event.detail);"
        @popstate.window="historyAction($event)"
        class="font-sans antialiased text-sm transition-colors hide-scroll ">
        <div x-data ="{
            selected : false,
            allChats : {},
            name : '',
            leads : [],
            lead : [],
            remarks : [],
            answers: [],
            questions : [],
            followups : [],
            showresults : false,
            fromDate : null,
            toDate : null,
            searchtype : 'scheduled_date',
            searchResults : null,
            pagination_data : null,
            searchFormState: [],
            searchFilter : null,
            sidedrawer : false,
            latest : null,
            processing : false,
            pollingID : setInterval(function(){
                $dispatch('checkforupdates');
            },5000)
        }"
        @checkforupdates.window="
        if(latest != null && !processing){
            processing = true;
            axios.get('api/messages/poll',{
                params:{
                    user_id : '{{Auth::user()->id}}',
                    latest : latest
                }
            }).then((r)=>{
                console.log(r.data);
                if(r.data.status == true){
                    let newMessages = r.data.new_messages;
                    newMessages.forEach((msg)=>{
                        if(allChats[msg.lead_id] != null && allChats[msg.lead_id] != null){
                            allChats[msg.lead_id].push(msg);
                        }
                        else{
                            allChats[msg.id] = [];
                            allChats[msg.id].push(msg);
                        }
                        latest = msg.id;
                    })
                    console.log(r.data.new_messages);
                }
                else{
                    console.log('No new messages');
                }
            }).catch((e)=>{
                console.log(e);
            });
            processing = false;
        }"
         class="min-h-screen bg-base-200 flex flex-col" >

            <main class="flex flex-col items-stretch  flex-grow w-full ">
                <div x-data="{show: true}" x-show="show"
                @contentupdate.window="
                if ($event.detail.target == 'renderedpanel') {
                    show = false;
                    setTimeout(() => {
                        $el.innerHTML = $event.detail.content;
                        show = true;},
                        400
                    );
                }
                " id="renderedpanel"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="translate-x-6"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-250"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-6"
                class="">
                @fragment('page-content')
                    {{ $slot }}
                @endfragment

                </div>

            </main>
        </div>
        <x-easyadmin::display.notice />
        <x-easyadmin::display.toast />
        <x-display.loading/>

        @stack('js')

    </body>
</html>
