export default (ctx, inject) => {
  const baseUrl = {
    BACKEND_URL: process.env.BACKEND_URL,
    BACKEND_PORT: process.env.BACKEND_PORT,
    ROULETTE_PORT: process.env.ROULETTE_PORT,
    LADDER_PORT: process.env.LADDER_PORT,
    CRASH_PORT: process.env.CRASH_PORT,
    JACKPOT_PORT: process.env.JACKPOT_PORT
  }

  inject('baseUrl', baseUrl)
}
