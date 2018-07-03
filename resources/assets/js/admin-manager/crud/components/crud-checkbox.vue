<template>
    <v-checkbox
            :true-value="1"
            :false-value="0"
            v-model="checked"
            :value="value"
            hide-details
    ></v-checkbox>

</template>

<script>

    export default {
        name: "crud-checkbox",
        model:{
            prop: "value",
            event: "change"
        },
        props: {
           field: {},
           value: { default: 0}
        },
        data: function () {
            return {
                checked: 0
            }
        },
        watch:{
            checked(val, oldVal){
                this.$emit("change", this.checked );
            },
            value(val, oldVal){
                this.checked = val ? +val : 0;
            }
        },
        beforeMount(){
            this.checked = 0;
        }
    }
</script>
