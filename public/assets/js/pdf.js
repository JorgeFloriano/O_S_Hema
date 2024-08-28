window.onload = function(){
    document.getElementById("btnPdf")
    .addEventListener("click", ()=>{
        const relatorio = this.document.getElementById("relatorio");
        const name = this.document.getElementById("header3").innerText;
        console.log(relatorio);
        var opt = {
            margin:        0,
            filename:     name+'.pdf',
            image:        { type: 'jpeg'},
            html2canvas: { scale: 4, y: 0,  scrollY: 0},
            jsPDF:        { format: 'A4', orientation: 'portrait' }
          };
        html2pdf().from(relatorio).set(opt).save();
    })
}