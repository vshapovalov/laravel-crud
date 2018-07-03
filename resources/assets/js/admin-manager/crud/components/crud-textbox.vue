<template>
    <v-text-field
            v-model="text"
            hide-details
            :type="inputType"
            class="px-0 py-0"
            :mask="mask"
            :prefix="prefix"
            :suffix="suffix"
            solo
            clearable
    ></v-text-field>
</template>

<script>
    export default {
        name: "crud-textbox",
        model:{
            prop: "value",
            event: "change"
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
        computed: {
            inputType(){
                return (this.field.additional && this.field.additional.mode === 'password') ? 'password' : 'text';
            },
            mask(){ return (this.field.additional && this.field.additional.mask) ? this.field.additional.mask : ''},
            prefix(){ return (this.field.additional && this.field.additional.prefix) ? this.field.additional.prefix : ''},
            suffix(){ return (this.field.additional && this.field.additional.suffix) ? this.field.additional.suffix : ''}
        },
        methods: {
            onChange(){
                this.$emit("change", this.text == null ? '' : this.text);
            }
        }
    }
</script>
