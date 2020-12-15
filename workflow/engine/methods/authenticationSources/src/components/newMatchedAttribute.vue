<template>
    <div>
        <titleSection :title="$root.translation('ID_NEW_MATCHED_ATTRIBUTE')"></titleSection>
        <b-form @submit.stop.prevent="onSave">
            <b-container fluid>
                <b-row>
                    <b-col>
                        <b-form-group :label="$root.translation('ID_ROLE')">
                            <b-form-select v-model="form.attributeRole"
                                           :options="roles"/>
                        </b-form-group>
                        <b-form-group :label="$root.translation('ID_PROCESSMAKER_USER_FIELD')" description="">
                            <b-form-select v-model="form.attributeUser"
                                           :options="userAttributes"/>
                        </b-form-group>
                        <b-form-group :label="$root.translation('ID_LDAP_ATTRIBUTE')">
                            <b-form-input v-model="form.attributeLdap"
                                          :state="validateState('attributeLdap')"
                                          placeholder=""/>
                            <b-form-invalid-feedback>{{$root.translation('ID_IS_REQUIRED')}}</b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row class="text-right">
                    <b-col>
                        <b-form-group>
                            <b-button variant="danger" @click="$emit('cancel')">{{$root.translation('ID_CANCEL')}}</b-button>&nbsp;
                            <b-button type="submit" variant="success">{{$root.translation('ID_SAVE')}}</b-button>
                        </b-form-group>
                    </b-col>
                </b-row>
            </b-container>
        </b-form>
    </div>
</template>

<script>
    import { validationMixin } from "vuelidate"
    import { required } from "vuelidate/lib/validators"
    import titleSection from "./titleSection.vue"
    export default {
        mixins: [validationMixin],
        components: {
            titleSection
        },
        validations: {
            form: {
                attributeLdap: {
                    required
                }
            }
        },
        data() {
            return {
                form: {
                    index: null,
                    attributeLdap: "",
                    attributeRole: "",
                    attributeUser: "USR_FIRSTNAME"
                },
                roles: [
                    {value: "", text: "All"},
                    {value: "PROCESSMAKER_ADMIN", text: this.$root.translation("ID_SYSTEM_ADMINISTRATOR")},
                    {value: "PROCESSMAKER_MANAGER", text: this.$root.translation("ID_MANAGER")},
                    {value: "PROCESSMAKER_OPERATOR", text: this.$root.translation("ID_OPERATOR")}
                ],
                userAttributes: [
                    {value: "USR_FIRSTNAME", text: "USR_FIRSTNAME"},
                    {value: "USR_LASTNAME", text: "USR_LASTNAME"},
                    {value: "USR_EMAIL", text: "USR_EMAIL"},
                    {value: "USR_DUE_DATE", text: "USR_DUE_DATE"},
                    {value: "USR_STATUS", text: "USR_STATUS"},
                    {value: "USR_STATUS_ID", text: "USR_STATUS_ID"},
                    {value: "USR_ADDRESS", text: "USR_ADDRESS"},
                    {value: "USR_PHONE", text: "USR_PHONE"},
                    {value: "USR_FAX", text: "USR_FAX"},
                    {value: "USR_CELLULAR", text: "USR_CELLULAR"},
                    {value: "USR_ZIP_CODE", text: "USR_ZIP_CODE"},
                    {value: "USR_POSITION", text: "USR_POSITION"},
                    {value: "USR_BIRTHDAY", text: "USR_BIRTHDAY"},
                    {value: "USR_COST_BY_HOUR", text: "USR_COST_BY_HOUR"},
                    {value: "USR_UNIT_COST", text: "USR_UNIT_COST"},
                    {value: "USR_PMDRIVE_FOLDER_UID", text: "USR_PMDRIVE_FOLDER_UID"},
                    {value: "USR_BOOKMARK_START_CASES", text: "USR_BOOKMARK_START_CASES"},
                    {value: "USR_TIME_ZONE", text: "USR_TIME_ZONE"},
                    {value: "USR_DEFAULT_LANG", text: "USR_DEFAULT_LANG"},
                    {value: "USR_LAST_LOGIN", text: "USR_LAST_LOGIN"}
                ]
            };
        },
        methods: {
            validateState(name) {
                const {$dirty, $error} = this.$v.form[name];
                return $dirty ? !$error : null;
            },
            onSave() {
                this.$v.form.$touch();
                if (this.$v.form.$anyError) {
                    return;
                }
                this.$emit("save", this.form);
            },
            load(row, index) {
                this.form.index = index;
                this.form.attributeLdap = row.attributeLdap;
                this.form.attributeRole = row.attributeRole;
                this.form.attributeUser = row.attributeUser;
            },
            reset() {
                this.form = {
                    index: null,
                    attributeLdap: "",
                    attributeRole: "",
                    attributeUser: "USR_FIRSTNAME"
                };
            }
        }
    }
</script>

<style scoped>
</style>
