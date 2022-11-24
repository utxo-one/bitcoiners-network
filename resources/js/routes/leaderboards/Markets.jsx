// import { CompactNumberFormat } from "../../utils/NumberFormatting";

import './Markets.scss';

const SHITCOIN_NAMES = {
'Ethereum'          : "Wefereum",
'Tether'            : "Untethers",
'BNB'               : "BRB",
'USD Coin'          : 'Fiat Shitcoin #1',
'Binance USD'       : 'Fiat Shitcoin #2',
'XRP'               : 'Ripoff',
'Cardano'           : "Cardanus",
'Polygon'           : "ProllyGone",
'Polkadot'          : "Polkanot",
'Lido Staked Ether' : "CantWithdraw",
'Dai'               : "Gai",
'Shiba Inu'         : "Shita Inu",
'OKB'               : "OKBroke",
'TRON'              : "PAWN",
'Solana'            : "Samlana",
'Litecoin'          : "BlightCoin",
'Uniswap'           : "EunuchSwap",
'Wrapped Bitcoin'   : "Not Bitcoin",
'Avalanche'         : "Anal Lance",
'Chainlink'         : "Chainstink",
'Cosmos Hub'        : "Commy Hub",
'Ethereum Classic'  : "NoGPU4U",
};

export default function Markets({ markets }) {
  return (
    <div className="__markets">
      <table className="__markets">
        <thead>
          <tr>
            <th className='rank'>#</th>
            <th className='coin'>Coin</th>
            <th className='price'>Price</th>
            {/* <th className='cap'>Cap</th> */}
          </tr>
        </thead>

        <tbody>
          { markets?.map((item, index) => (
            <tr key={item.id}>
              <td className='rank'>{ index + 1 }</td>
              <td className="coin">
                { item.id === 'bitcoin' ? <img src={item.image} /> : <span className="poop">💩</span> }
                { SHITCOIN_NAMES[item.name] || item.name }
                <span className="symbol">{ item.symbol }</span>
              </td>
              <td className="price">{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(item.current_price) }</td>
              {/* <td className='cap'>{ CompactNumberFormat(item.market_cap, { digits: 3 }) }</td> */}
          </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
