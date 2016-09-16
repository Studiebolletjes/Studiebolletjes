var MessageBox = {
    Show: function (text) {
        alert(text);
    },
    
    Error: function(){
        this.Show("Er is een fout opgetreden.")
    }
};