<div class="dropdown ">
    <label tabindex="0" class="btn btn-sm" ><span x-text="selected_action"></span><x-icons.down-arrow /></label>
    <ul tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52"
        :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral'">
        <li>
            <a @click.prevent.stop=" selected_action = '-- Select Action --' "
                class="" :class="selected_action == '' ? ' text-primary hover:text-primary' : ''"> -- Select Action --
            </a>
        </li>
        <li>
            <a @click.prevent.stop=" selected_action = 'Add Followup' "
                class="" :class="selected_action == 'Add Followup' ? ' text-primary hover:text-primary' : ''">Add Followup
            </a>
        </li>
        <li x-show="lead.status != 'Consulted' && lead.status != 'Appointment Fixed'">
            <a @click.prevent.stop="selected_action = 'Schedule Appointment'"
                class=" " :class="selected_action == 'Schedule Appointment' ? ' text-primary hover:text-primary' : ''">Schedule Appointment
            </a>
        </li>
        <li x-show="lead.status != 'Closed'">
            <a @click.prevent.stop=" selected_action = 'Close Lead' "
                class="" :class="selected_action == 'Close Lead' ? ' text-primary hover:text-primary' : ''">Close Lead
            </a>
        </li>
    </ul>
</div>
