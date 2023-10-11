<template>
    <form>
        <div class="row">
            <div class="col-lg-6">
                <label for="chosenFund">Choose a fund to invest in</label>
                <select
                    id="chosenFund"
                    class="form-control"
                    v-model="formDetails.chosenFund"
                    :class="{
                    'is-invalid': $v.formDetails.chosenFund.$invalid
                    && $v.formDetails.chosenFund.$dirty,
                    'is-valid': !$v.formDetails.chosenFund.$invalid,
                    }"
                >
                    <option value="" selected disabled>
                        Please select an option
                    </option>
                    <option v-for="(fund, index) in funds" :key="index" :value="fund.id">
                        {{ fund.name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <label for="chosenAmount">How much do you want to invest?</label>
                <input
                    id="chosenAmount"
                    v-model="formDetails.chosenAmount"
                    class="form-control"
                    :class="{
                    'is-invalid': $v.formDetails.chosenAmount.$invalid
                    && $v.formDetails.chosenAmount.$dirty,
                    'is-valid': !$v.formDetails.chosenAmount.$invalid,
                    }"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button
                    class="button is-success"
                    @click="save"
                >
                    Confirm
                </button>
            </div>
        </div>
    </form>
</template>

<script>

import { getDefaultRequestConfiguration } from '../../components/requests';

export default {
  name: "DepositSelect",
  data() {
    return {
      formDetails: {
        chosenFund: null,
        chosenAmount: null
      },
      funds: [],
      allowanceRemaining: null,
    }
  },
  validations() {
    formDetails: {
      chosenFund: {
        required
      },
      chosenAmount: {
        required,
        decimal,
        minValue: minValue(1),
        maxValue: maxValue(20000)
      }
    }
  },
  computed: {
    ...mapGetters({
      account: "account/account",
    }),
  },
  mounted() {
    axios
      .get(
        '/api/funds',
        getDefaultRequestConfiguration()
      )
      .then((data) => {
        this.funds = data.data.data;
      })
      .catch(error => this.handleError(error));
    axios
      .get(
        '/api/accounts/' + this.account.id + '/allowance-remaining',
        getDefaultRequestConfiguration()
      )
      .then((data) => {
        this.allowanceRemaining = data.data.data;
      })
      .catch(error => this.handleError(error));
  },
  methods: {
    save() {
      this.$v.$touch();
      if (!this.$v.$invalid) {
        if (this.allowanceRemaining > this.formDetails.chosenAmount)  {
          axios
            .post(
              '/api/investments',
              {
                accountId: this.account.id,
                fundId: this.formDetails.chosenFund,
                amount: this.formDetails.chosenAmount,  
              },
              getDefaultRequestConfiguration()
            )
            .then(() => {
              this.$toaster.success('Investment secured.');
            })
            .catch((error) => {
              this.handleError(error);
            });
        } else {
          this.$toaster.error(
            'Your remaining yearly allowance of £' 
            + this.allowanceRemaining + 
            ' is not enough to make this investment.'
          );
        }
      } else {
        this.$toaster.error('Please check your answers and try again.');
      }
    },
  },
};

</script>

<style scoped>

</style>
