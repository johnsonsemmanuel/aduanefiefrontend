import Typography from "@mui/material/Typography";
import FormControl from "@mui/material/FormControl";
import cls from "./payment-type.module.css";
import FormControlLabel from "@mui/material/FormControlLabel";
import { RadioInput } from "../radio-input/index.js";
import RadioGroup from "@mui/material/RadioGroup";

export const PaymentType = ({ payments, defaultPayment, onChange }) => {
  return (
    <>
      <Typography
        px={2}
        variant="subtitle1"
        fontSize={18}
        fontWeight={600}
        mb={1.5}
      >
        Payment type
      </Typography>
      <FormControl sx={{ px: "1rem", width: "calc(100% - 2rem)", mb: 2 }}>
        <RadioGroup
          aria-labelledby="demo-controlled-radio-buttons-group"
          name="controlled-radio-buttons-group"
          value={defaultPayment}
          onChange={(e) => onChange(e.target.value)}
          sx={{ gap: "10px" }}
        >
          {payments?.map((payment) => (
            <div
              className={cls.card}
              key={payment?.id}
              onClick={() => onChange(payment?.tag)}
            >
              <FormControlLabel
                value={payment?.tag}
                control={<RadioInput />}
                label={payment?.tag}
              />
            </div>
          ))}
        </RadioGroup>
      </FormControl>
    </>
  );
};
