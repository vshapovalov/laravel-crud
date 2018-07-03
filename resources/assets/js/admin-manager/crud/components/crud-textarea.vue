<template>
    <v-textarea
            textarea
            v-model="text"
            hide-details
            solo
            auto-grow
            clearable
    ></v-textarea>
</template>

<script>
    export default {
        name: 'crud-textarea',
        model:{
            prop: 'value',
            event: 'change'
        },
        props: ['field', 'value'],
        data: function () {
            return {
                text: ''
            }
        },
        watch: {
            value:{
                handler(val, oldVal){
                    this.text = val;
                },
                immediate: true
            },
            text(val, oldVal){

                this.onChange();
            },
        },
        computed: {},
        methods: {
            onChange(){
                this.$emit("change", this.text == null ? '' : this.text);
            }
        },
    }
</script>
