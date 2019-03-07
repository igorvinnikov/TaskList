<template>
    <div>
        <bj-form :fields="fields" :data="payload" ref="form" @keyup.enter.native="submit">
            <div class="col-12" slot="start">
                <h5 class="no-margin underline-title text-bold">Sign in</h5>
            </div>
            <div class="col-xs-5 col-md-6" slot="end">
                <q-btn no-caps class="full-width" rounded @click="submit" color="primary" label="Sign in"/>
            </div>
            <div class="col-xs-7 col-md-6 text-right" slot="end" v-if="showSubmitScannerButton">
                <q-btn no-caps class="full-width" @click="submitScanner" label="Smart Login" rounded
                       icon="fingerprint"/>
            </div>
            <div class="col-xs-7 col-md-6 text-right" slot="end">
                <q-btn no-caps :to="{name: 'auth.forgot-password'}" :label="$t('loginScreen.forgotPasswordTitle')"
                       rounded flat/>
            </div>
            <div class="col-12" slot="end">
                <q-btn no-caps :to="{name: 'auth.register'}" class="full-width" label="Join" rounded
                       icon="far fa-user" color="secondary"/>
            </div>
        </bj-form>
        <bj-inner-loading :visible="loading" label="Signing in"/>
    </div>
</template>

<script>
    import ScannerAuthentication from "mixins/scanner-authentication";

    export default {
        mixins: [ScannerAuthentication],
        data() {
            return {
                loading: false,
                fields: [
                    {
                        name: "email",
                        type: "email",
                        label: "Email",
                        rules: ["required", "email"],
                        error: "You must fill a valid email address"
                    },
                    {
                        name: "password",
                        type: "password",
                        label: "Password",
                        rules: ["required"],
                    }
                ],
                payload: {
                    email: "",
                    password: ""
                }
            };
        },
        created() {
            console.log("query", this.$route.query);
            if (
                this.$route.query != undefined &&
                this.$route.query.verified != undefined
            ) {
                this.uiAlert(
                    this.$t('loginScreen.accountReadyText'),
                    this.$t('loginScreen.accountReadyTitle')
                );
                this.$router.push({
                    query: {}
                });
            }
        },
        computed: {
            showSubmitScannerButton() {
                if (
                    this.$store.getters["auth/scannerAvailable"] == true &&
                    this.$store.getters["auth/scannerActivated"] == true &&
                    this.$store.getters["auth/scannerData"] != null
                ) {
                    if (this.payload.email.length == 0) {
                        return true;
                    } else {
                        if (
                            this.payload.email !=
                            this.$store.getters["auth/scannerData"].email
                        ) {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        },
        methods: {
            redirectAfterLogin() {
                if (
                    this.userRoleIs(this.$config.roles.PROPERTY_MANAGER) // &&
                // this.$store.getters["auth/user"].properties_count == 0
                ) {
                    this.redirect("app.properties");
                } else {
                    if (this.userRoleIs(this.$config.roles.AGENT)) {
                        this.redirect(this.$q.platform.is.cordova ? 'app.agent.home' : 'app.agent.tours');
                    } else {
                        this.redirect("app.map");
                    }
                }
            },
            submitScanner() {
                let vm = this;
                let config = {
                    email: vm.$store.getters["auth/scannerData"].email,
                    token: vm.$store.getters["auth/scannerData"].token
                };
                this.scannerDecrypt(config).then(payload => {
                    console.log("payload for simpleLogin", payload);
                    vm.loading = true;
                    vm
                        .simpleLogin(payload, false)
                        .then(response => {
                            vm.loading = false;
                            vm.uiNotify("Welcome " + response.user.full_name);
                            vm.redirectAfterLogin();
                        })
                        .catch(() => {
                            console.log("error", arguments);
                            vm.loading = false;
                            vm.$store.commit("auth/SET_SCANNER_DATA", null);
                            vm.$store.commit("auth/SET_SCANNER_ACTIVATED", null);
                            vm.uiAlert(
                                "Something went wrong, please re-login",
                                "Smart Login Failed"
                            );
                        });
                });
            },
            simpleLogin(payload = null, showNotifications = true) {
                let vm = this;

                if (payload == null) payload = vm.payload;

                return new Promise((resolve, reject) => {
                    vm
                        .api()
                        .request("POST", "auth/login", payload)
                        .then(response => {
                            this.$store.commit("auth/LOGIN", {
                                token: response.token,
                                user: response.user
                            });
                            resolve(response);
                        })
                        .catch(response => {
                            if (showNotifications) {
                                if (response.error == "Unauthorized") {
                                    this.uiNotify(
                                        "Invalid credentials",
                                        "negative"
                                    );
                                } else if (typeof response.errors.stripe !== 'undefined') {
                                    this.uiAlert(`You didn't configure Stripe account. We sent you an email right now, please enter the link again.`);
                                } else {
                                    this.$q.notify({
                                        message: "Your account is not verified yet",
                                        icon: 'error_outline',
                                        color: 'black',
                                        actions: [
                                            {
                                                label: 'Resend mail',
                                                handler: () => {
                                                    this.api().request('POST', 'send-verification-email', {
                                                        email: payload.email
                                                    }).then(() => {
                                                        this.uiNotify('Verification email sent.');
                                                    });
                                                }
                                            }
                                        ]
                                    })
                                }
                            }
                            reject(response.error);
                        });
                });
            },
            submit() {
                let vm = this;
                this.$refs.form.touch();
                this.$refs.form.validate().then(
                    () => {
                        if (this.payload.email == 'dev@test.com' && this.payload.password == 'test') {
                            this.bus().emit('open-switcher')
                        } else {
                            this.loading = true;
                            this.simpleLogin()
                                .then(response => {
                                    this.loading = false;
                                    this.scannerIsAvailable()
                                        .then(() => {
                                            if (
                                                vm.$store.getters[
                                                    "auth/scannerActivated"
                                                    ] == null
                                            ) {
                                                vm.bus().emit("scanner-modal:open", {
                                                    type: "activate-from-login",
                                                    loginResponse: response,
                                                    loginPayload: vm.payload
                                                });
                                            } else {
                                                if (
                                                    vm.$store.getters[
                                                        "auth/scannerRequested"
                                                        ] == true
                                                ) {
                                                    vm
                                                        .bus()
                                                        .emit("scanner-modal:encrypt", {
                                                            loginResponse: response,
                                                            loginPayload: vm.payload
                                                        });
                                                } else {
                                                    vm.$store.commit(
                                                        "auth/SET_FLOW",
                                                        "simple"
                                                    );
                                                    vm.uiNotify(
                                                        "Welcome " +
                                                        response.user.full_name
                                                    );
                                                    vm.redirectAfterLogin();
                                                }
                                            }
                                        })
                                        .catch(() => {
                                            vm.$store.commit("auth/SET_FLOW", "simple");
                                            vm.uiNotify(
                                                "Welcome " + response.user.full_name
                                            );
                                            vm.redirectAfterLogin();
                                        });
                                })
                                .catch(() => {
                                    this.loading = false;
                                });
                        }
                    },
                    () => {
                        this.uiNotify("Please fill email & password", "black");
                    }
                );
            }
        }
    };
</script>

<style>
</style>
