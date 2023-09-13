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


        }" class="min-h-screen bg-base-200 flex flex-col">

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
