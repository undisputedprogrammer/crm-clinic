import axios from "axios";
export default () => ({
    mode: "add",
    id: "",
    name: "",
    email: "",
    center_id: "",
    audits:[],
    auditsLoading: false,
    journalsLoading: true,
    journals: [],
    formatDate(isoDateString) {
        const options = { year: "numeric", month: "short", day: "2-digit" };
        return new Date(isoDateString).toLocaleDateString("en-US", options);
    },
    formatTime(isoDateString) {
        const date = new Date(isoDateString);
        const hours = (date.getHours() % 12 || 12).toString().padStart(2, "0");
        const minutes = date.getMinutes().toString().padStart(2, "0");
        const ampm = date.getHours() >= 12 ? "PM" : "AM";

        return `${hours}:${minutes} ${ampm}`;
    },
    reset() {
        this.id = '';
        this.name = '';
        this.email = '';
        this.center_id = '';
        mode = 'add';
    },
    fetchaudits(){
        this.auditsLoading = true;
        axios.get('/fetch/audits',{
            params:{
                user_id : this.id
            }
        }).then((r)=>{
            console.log(r.data);
            this.audits = r.data;
        }).catch((e)=>{
            this.$dispatch('showtoast',{mode:'error',message: 'Could not load audits'});
        });
        this.auditsLoading = false;
    },
    fetchJournals(){
        this.journalsLoading = true;
        axios.get('/fetch/journals',{
            params:{
                user_id : this.id
            }
        }).then((r)=>{
            console.log(r.data);
            this.journals = r.data;
        }).catch((e)=>{
            this.$dispatch('showtoast',{mode:'error',message: 'Could not load journals'});
        });
        this.journalsLoading = false;
    },
    filterJournals(el){
        let formdata = new FormData(el);
        console.log(formdata.get('month'));
        axios.get('/fetch/journals',{
            params:{
                month: formdata.get('month')
            }
        }).then((r)=>{
            console.log(r.data);
        }).catch((e)=>{
            console.log(e);
        });
    }
});
