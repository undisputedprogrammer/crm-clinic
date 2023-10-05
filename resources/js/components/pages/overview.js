import axios from "axios";

export default () => ({
    journal: null,
    journalSubmit(formID, url, route) {
        let formdata = new FormData(document.getElementById(formID));
        if(this.journal != null){
            let currentBody = this.journal.body;
            let newBody = currentBody+"\n"+formdata.get('body');
            formdata.set('body',newBody);
        }
        this.$dispatch("formsubmit", {
            url: url,
            route: route,
            fragment: "page-content",
            formData: formdata,
            target: formID,
        });
    },
    postJournalSubmission(content) {
        if (content.success == true) {
            this.$dispatch("showtoast", {
                message: content.message,
                mode: "success",
            });
            if(content.journal != null && content.journal != undefined){
                if(this.journal == null){
                    this.journal = content.journal;
                }else{
                    this.journal.body = content.journal.body;
                }
            }
        } else if (content.success == false) {
            this.$dispatch("showtoast", {
                message: content.message,
                mode: "error",
            });
        }
    },
    getDate() {
        var today = new Date();
        var monthNames = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];

        var day = today.getDate();
        var monthIndex = today.getMonth();
        var year = today.getFullYear();
        var formattedDay = day < 10 ? "0" + day : day;
        var monthName = monthNames[monthIndex];
        var formattedDate = formattedDay + " " + monthName + " " + year;

        return formattedDate;
    },
});
