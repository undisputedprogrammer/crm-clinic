import axios from "axios";

export default () => ({
    sendingMessages : false,
    selectedLeads: {},
    selectLead(el, lead) {
        if (el.checked) {
            this.selectedLeads[lead.id] = lead.phone;
        } else {
            delete this.selectedLeads[lead.id];
        }

        console.log(Object.keys(this.selectedLeads).length);
    },
    sendBulkMessage(ajaxLoading){
        if(Object.keys(this.selectedLeads).length < 1){
            console.log('No leads selected');
        }
        else{
            this.sendingMessages = true;
            setTimeout(()=>{
                axios.post('/message/bulk/sent',{
                    numbers : this.selectedLeads,
                    template : document.getElementById('selectTemplate').value
                }).then((r)=>{
                    console.log(r.data);
                    this.sendingMessages = false;
                    this.$dispatch('showtoast',{'mode':'success','message':'Messages scheduled for sending'});
                    this.selectedLeads = {};
                }).catch((e)=>{
                    console.log(e);
                    this.sendingMessages = false;
                    this.$dispatch('showtoast',{'mode':'error','message':'Messages not sent, please try after some time.'});
                });

            },500)

        }
    }
});

