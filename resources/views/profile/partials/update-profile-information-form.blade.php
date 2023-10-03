<section>
    <header>
        <h2 class="text-lg font-medium text-base-content">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-base-content">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form x-data="{
            doSubmit(){
                let form = document.getElementById('profile-update-form');
                let formdata = new FormData(form);
                $dispatch('formsubmit',{url:'{{route('profile.save')}}', route: 'profile.save',fragment: 'page-content', formData: formdata, target: 'profile-update-form'});
            }
        }"
        @submit.prevent.stop="doSubmit();"
        @formresponse.window="
            if ($event.detail.target == $el.id) {
                if ($event.detail.content.success) {
                        $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                        $dispatch('linkaction',{link: '{{route('user.profile')}}', route: 'user.profile', fragment: 'page-content', fresh: true});
                        $dispatch('formerrors', {errors: []});
                    } else if (typeof $event.detail.content.errors != undefined) {
                        $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                    } else{
                        $dispatch('formerrors', {errors: $event.detail.content.errors});
                    }
            }
        "
        id="profile-update-form" class="mt-6 space-y-6 flex w-full justify-evenly items-start">
        @csrf

        <div>
            @php
                $element = [
                'key' => 'user_picture',
                'label' => 'Profile Picture',
                'authorised' => true,
                'validations' => [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
                ];
            @endphp
            <x-easyadmin::inputs.imageuploader :element="$element"/>
        </div>

        <div class=" flex flex-col space-y-2">

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block min-w-72 text-black" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>

    </div>


    </form>
</section>
