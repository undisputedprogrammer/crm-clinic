import axios from "axios";
export default ()=>({
    selected : false,
    showTemplateModal : false,
    selectedCenter : null,
    theLink : null,
    is_genuine : null,
    is_valid : null,
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
        let is_valid = formdata.get('is_valid');
        let is_genuine = formdata.get('is_genuine');
        let params = {};
        if(status != null && status != ''){
            params.status = status;
        }
        if(is_valid != null && is_valid != ''){
            is_valid = (is_valid === 'true');
            params.is_valid = is_valid;
        }
        if(is_genuine != null && is_genuine != ''){
            is_genuine = (is_genuine === 'true');
            params.is_genuine = is_genuine;
        }
        let selectedCenter = document.getElementById('select-center').value;
        params.center = selectedCenter;
        this.$dispatch('linkaction',{link: this.theLink, route: 'fresh-leads', fragment: 'page-content', fresh: true, params: params});
    }

});
