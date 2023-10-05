import axios from "axios";
export default ()=>({
    showTemplateModal : false,
    selectedCenter : null,
    theLink : null,
    toggleTemplateModal(){
        this.showTemplateModal = !this.showTemplateModal;
    },
    selectTemplate(el){
        let formdata = new FormData(el);
        console.log(formdata);
    },
    searchlead(){
        let formdata = new FormData(document.getElementById('lead-search-form'));
        let searchString = formdata.get('search');

        this.$dispatch('linkaction',{link: this.theLink, route:'fresh-leads',fragment:'page-content',fresh: true, params:{search: searchString}});
    },
    filterByStatus(el){
        let formdata = new FormData(el);
        let status = formdata.get('status');

        this.$dispatch('linkaction',{link: this.theLink, route: 'fresh-leads', fragment: 'page-content', fresh: true, params:{status: status}});
    }

});
