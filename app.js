new Vue({
    el: '#app',
    data: {
        optionList: [],
        startPoint: '',
        distance: 50,
        result: []
    },
    watch: {
        startPoint: 'getData',
        distance: 'getData'
    },
    methods: {
        async getData() {
            if (!this.startPoint || !this.distance) {
                return [];
            }
            try {
                const response = await window.fetch(`./data.php?startPoint=${this.startPoint}&distance=${this.distance}`);
                const data = await response.json();
                this.$set(this, 'result', data);
            } catch (error) {
                console.error(error);
                this.$set(this, 'result', []);
            }
        }
    },
    mounted(){
        window.fetch('./data.php?getDictionary')
        .then((response) => {
            return response.json();
        }).then((data) => {
            this.$set(this, 'optionList', data);
        }).catch((error) => {
            console.error(error);
        });
    }
});
