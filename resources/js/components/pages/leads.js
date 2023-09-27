export default ()=>({
    showTemplateModal : false,
    selectedCenter : null,
    toggleTemplateModal(){
        this.showTemplateModal = !this.showTemplateModal;
    },
    selectTemplate(el){
        let formdata = new FormData(el);
        console.log(formdata);
    }

});
