<template>
    <input ref="input" class="input" :type="inputType" :value="value" @input="onChange" />
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
            return {}
        },
        computed: {
            inputType(){
                if (this.field.additional && this.field.additional.mode === 'password') {

                    return 'password';
                } else {

                    return 'text';
                }
            },
        },
        methods: {
            onChange($event){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                if (this.field.additional && this.field.additional.mode === 'masked' && this.field.additional.mask) {

                    this.$emit("change", $(this.$refs.input).masked($event.target.value));

                } else {

                    this.$emit("change", $event.target.value);
                }
            }
        },
        mounted() {
            if (this.field.additional) {

                if (this.field.additional.mode === 'password') {
                    this.$emit("change", "");
                } else {
                    if (this.field.additional.mode === 'masked' && this.field.additional.mask) {

                        let maskOptions = {
                            clearIfNotMatch: true,
                            translation: {
                                'P': {pattern: /[,.]/, optional: true}
                            }
                        };

                        if (this.field.additional.placeholder){
                            maskOptions['placeholder'] = this.field.additional.placeholder;
                        }

                        if (this.field.additional.reverse){
                            maskOptions['reverse'] = this.field.additional.reverse;
                        }

                        $(this.$refs.input).mask(this.field.additional.mask, maskOptions);
                    }
                }

            }


        }
    }
</script>
