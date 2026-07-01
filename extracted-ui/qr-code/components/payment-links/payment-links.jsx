import Container from "@mui/material/Container";
import cls from "./payment-links.module.css";
import Stack from "@mui/material/Stack";
import { QRCodeCanvas } from "qrcode.react";
import { useState } from "react";

const PaymentLinks = ({ links = [] }) => {
  const [selectedLink, setSelectedLink] = useState(
    links?.length ? links[0] : "",
  );
  console.log("selectedLink", selectedLink);
  return (
    <Container maxWidth="sm" className={cls.container}>
      {!!selectedLink?.length && (
        <div className={cls.qrContainer}>
          <QRCodeCanvas
            size={300}
            id={selectedLink}
            value={selectedLink}
            style={{
              aspectRatio: "1/1",
              height: "100%",
            }}
            level="H"
            bgColor="#fff"
          />
          <a href={selectedLink} className={cls.link}>
            Navigate
          </a>
        </div>
      )}
      {links?.length > 1 && (
        <>
          <h1 className={cls.title}>Payments link</h1>
          <Stack spacing={1} px={2}>
            {links.map((link, index) => (
              <button
                type="button"
                className={
                  link === selectedLink
                    ? `${cls.item} ${cls.selectedItem}`
                    : cls.item
                }
                key={link}
                onClick={(e) => {
                  e.preventDefault();
                  setSelectedLink(link);
                }}
              >
                Link to payment {index + 1}
              </button>
            ))}
          </Stack>
        </>
      )}
    </Container>
  );
};

export default PaymentLinks;
