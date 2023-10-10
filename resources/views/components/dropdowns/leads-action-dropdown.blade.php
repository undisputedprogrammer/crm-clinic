<div x-show="lead.status == 'Created'" class="dropdown ">
    <span x-text="lead.status"></span>
    <label tabindex="0" class="btn btn-sm" ><span x-text="selected_action"></span><x-icons.down-arrow /></label>
    <ul tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52"
        :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral'">
        <li>
            <a @click.prevent.stop="selected_action = 'Initiate Followup' "
                class=" " :class="selected_action == 'Initiate Followup' ? ' text-primary hover:text-primary' : ''">Initiate Followup
            </a>
        </li>
        <li>
            <a @click.prevent.stop="selected_action = 'Schedule Appointment'"
                class=" " :class="selected_action == 'Schedule Appointment' ? ' text-primary hover:text-primary' : ''">Schedule Appointment
            </a>
        </li>
        <li>
            <a @click.prevent.stop=" selected_action = 'Close Lead' "
                class="" :class="selected_action == 'Close Lead' ? ' text-primary hover:text-primary' : ''">Close Lead
            </a>
        </li>
    </ul>
</div>
