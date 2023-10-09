import axios from "axios";
import Chart from "chart.js/auto";
export default () => ({
    journal: null,
    chartCanvas : null,
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
    initChart(){
        new Chart(this.chartCanvas, {
            type: 'pie',
            data: {
                labels: ['Item-1', 'Item-2', 'Item-3', 'Item-4', 'Item-5'],
                datasets: [
                  {
                    label: 'Lead management process',
                    data: [ 418, 263, 434, 586, 332 ],
                    backgroundColor: [ "#51EAEA", "#FCDDB0",
                    "#FF9D76", "#FB3569", "#82CD47" ],
                  }
                ]
              },
            options: {
                responsive: true,
                plugins: {
                  legend: {
                    display: true,
                    position: 'bottom',
                  },
                  title: {
                    display: true,
                    text: 'Process Overview'
                  }
                }
              }
          });
    }
});
