<template>
    <v-textarea
            textarea
            :value="value"
            @input.native="onChange"
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
            return {}
        },
        computed: {},
        methods: {
            onChange(event){
                this.$emit("change", event.target.value)
            }
        },
    }
</script>
