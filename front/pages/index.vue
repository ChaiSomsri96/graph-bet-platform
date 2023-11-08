<template>
  <div class="firstpage-container">
    <banner-box />
    <title-and-content-box title="What's Quick bit" sub-title="Quick bitとは">
      <div slot="content-box" class="h-full">
        <div class="flex-wrap flex-row-hc-vc h-full p-l-20 p-r-20">
          <span class="fs18 description-box p-b-20 text-center">
            Quick
            bitは、いつでもクラッシュする可能性がある増加曲線からなるオンラインマルチプレイヤービットコインギャンブルゲームです。楽しくスリリングにあなたの資金を一気に数倍にすることが出来る新感覚ゲームとなっております。
          </span>
          <img src="/imgs/firstpage/notebook.png" class="p-b-20" />
        </div>
      </div>
    </title-and-content-box>
    <title-and-content-box title="How does it work?" sub-title="プレイ方法">
      <div slot="content-box">
        <div class="flex-wrap flex-row-hc-vc p-t-40">
          <image-back-step-box
            back-img="/imgs/firstpage/step_1_back.png"
            title-img="/imgs/firstpage/step_1_title.png"
            title-string="まずは会員登録"
            content-string="まずは会員登録をしましょう。フォームに情報を入力し、すぐに申し込みを始めることが可能です。"
          />
          <image-back-step-box
            back-img="/imgs/firstpage/step_2_back.png"
            title-img="/imgs/firstpage/step_2_title.png"
            title-string="実際にBETする"
            content-string="まずは会員登録をしましょう。フォームに情報を入力し、すぐに申し込みを始めることが可能です。"
          />
          <image-back-step-box
            back-img="/imgs/firstpage/step_3_back.png"
            title-img="/imgs/firstpage/step_3_title.png"
            title-string="キャッシュアウト"
            content-string="増えていく倍率を見て、バーストする前にキャッシュアウトしましょう。成功すればそのタイミングの倍率で資金が増えます。"
          />
        </div>
      </div>
    </title-and-content-box>
    <image-back-annot-box
      title="ソーシャル＆リアルタイム"
      back-img="/imgs/firstpage/annot_1_back.png"
    >
      <div slot="content-box" class="fs16">
        <div class="m-b-20">
          私たちのユニークなコミュニティの一員になりましょう。
          あなたの運命の上昇を見て、リアルタイムで友達と遊ぶ。
        </div>
        <div>
          友達リストやプライベートメッセージなどの新しいゲーム機能により、友達と簡単に話したり、ゲームで新しい友達を作ることができます。
        </div>
      </div>
    </image-back-annot-box>
    <image-back-annot-box
      title="公平なゲームシステム"
      back-img="/imgs/firstpage/annot_2_back.png"
    >
      <div slot="content-box" class="fs16">
        <span>
          のQuick bit結果は公正であると証明できます。
          ゲームのハッシュを確認して結果を計算するために使用できるサードパーティのスクリプトがあります。
          このスレッドで技術的な詳細を読むことができます。
        </span>
      </div>
    </image-back-annot-box>
    <image-back-annot-box
      title="Be the Bankroll"
      back-img="/imgs/firstpage/annot_3_back.png"
    >
      <div slot="content-box" class="fs16">
        <span>
          あなたの財産を短期間で増やしてみませんか？
          バンクロールは、ゲームマスターが勝者に支払うために使用するプール資金のことです。
          あなたはそれの一部を受け取ることにより、Quick
          bitの利益でお金を稼ぐことができます。
        </span>
      </div>
    </image-back-annot-box>
    <title-and-content-box
      title="Biggest Winners So Far"
      sub-title="総合ランキング"
    >
      <div slot="content-box" class="h-full">
        <div class="flex-wrap flex-col-hc-vc p-t-40 p-b-40 h-full">
          <top-users-box />
          <nuxt-link to="/main">
            <button3-d title="ゲーム開始" class="m-t-50" />
          </nuxt-link>
        </div>
      </div>
    </title-and-content-box>
  </div>
</template>

<script>
import BannerBox from '@/components/pc/firstpage/BannerBox.vue'
import Button3D from '@/components/pc/common/Button3D.vue'
import TitleAndContentBox from '@/components/pc/firstpage/TitleAndContentBox.vue'
import ImageBackStepBox from '@/components/pc/firstpage/ImageBackStepBox.vue'
import ImageBackAnnotBox from '@/components/pc/firstpage/ImageBackAnnotBox.vue'
import TopUsersBox from '@/components/pc/firstpage/TopUsersBox.vue'

export default {
  name: 'FirstPage',
  components: {
    BannerBox,
    Button3D,
    TitleAndContentBox,
    ImageBackStepBox,
    ImageBackAnnotBox,
    TopUsersBox
  },
  mounted() {}
}
</script>

<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.firstpage-container {
  max-width: 100%;
  line-height: 1;
  margin: 0px auto;
}

.description-box {
  max-width: 560px;
  line-height: 2;
}
</style>
