<template>
    <div
        id="home"
    >
        <div class="demo">
            <div class="container">
                <h5 >{{ $t("ID_CUSTOM_CASES_LISTS") }}</h5>
                <div class="x_content">
                    <b-container fluid>
                        <b-tabs content-class="mt-3">
                            <b-tab :title="$t('TO_DO')" active>
                                <Tables module="inbox"/>
                            </b-tab>
                            <b-tab :title="$t('ID_DRAFT')" lazy>
                                <Tables module="draft"/>
                            </b-tab>
                            <b-tab :title="$t('ID_UNASSIGNED')" lazy>
                                <Tables module="unassigned"/>
                            </b-tab>
                             <b-tab :title="$t('ID_PAUSED')" lazy>
                                <Tables module="paused"/>
                            </b-tab>
                        </b-tabs> 
                    </b-container>
                  
                </div> 
            </div>
        </div>
    </div>
</template>
<script>
import Tables from "./Tables";
export default {
    name: "CustomCaseList",
    components: {
        Tables
    },
    data() {
        return {
            
        };
    },
    mounted() {
       
    },
    methods: {
        
    }
};
</script>

<style lang="scss">
#home {
    padding-left: 0px;
    transition: 0.3s;
}
#home.collapsed {
    padding-left: 50px;
}
#home.onmobile {
    padding-left: 50px;
}

.container {
    max-width: 1500px;
}
</style>
