<template>
  <div class="wait_for_ajax panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">{{ T.wordsProblems }}</h3>
    </div>
    <table class="table problem-list">
      <thead>
        <tr>
          <th class="contains-long-desc">{{ T.wordsTitle }}</th>
          <th class="numericColumn">{{ T.wordsQuality }}</th>
          <th class="numericColumn">{{ T.wordsDifficulty }}</th>
          <th class="numericColumn">{{ T.wordsRatio }}</th>
          <th class="numericColumn">
            {{ T.wordsPointsForRank }} <a data-toggle="tooltip"
                href="https://blog.omegaup.com/el-nuevo-ranking-de-omegaup/"
                rel="tooltip"
                title=""
                v-bind:data-original-title="T.wordsPointsForRankTooltip"><img src=
                "/media/question.png"></a>
          </th>
          <th class="numericColumn"
              v-if="loggedIn">{{ T.wordsMyScore }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-bind:class="rowClassForProblem(problem.visibility)"
            v-for="problem in problems">
          <td>
            <span v-bind:class="`glyphicon ${iconClassForProblem(problem.visibility)}`"
                v-bind:title="iconTitleForProblem(problem.visibility)"></span> <a v-bind:href=
                "`/arena/problem/${problem.alias}`">{{ problem.title }}</a>
            <div class="tag-list"
                 v-bind:title="`${problem.tags.map(tag =&gt; tag.name).join(' ')}`"
                 v-if="problem.tags.length">
              <a v-bind:class="tag.autogenerated === '1' ? 'tag-autogenerated':'tag'"
                   v-bind:href="hrefForProblemTag(currentTags, tag.name)"
                   v-for="tag in problem.tags">{{ tag.name }}</a>
            </div>
          </td>
          <td class="numericColumn"
              v-if="problem.quality === null">—</td>
          <td class="tooltip_column"
              v-else=""><span data-wenk-pos="right"
                v-bind:data-wenk=
                "`${UI.formatString(T.wordsOutOf4, {Score: problem.quality.toFixed(1)})}`">{{
                qualityTags[parseInt(problem.quality)] }}</span></td>
          <td class="numericColumn"
              v-if="problem.difficulty === null">—</td>
          <td class="tooltip_column"
              v-else=""><span data-wenk-pos="right"
                v-bind:data-wenk=
                "`${UI.formatString(T.wordsOutOf4, {Score: problem.difficulty.toFixed(1)})}`">{{
                difficultyTags[parseInt(problem.difficulty)] }}</span></td>
          <td class="numericColumn">{{ (100.0 * problem.ratio).toFixed(2) }}%</td>
          <td class="numericColumn">{{ problem.points.toFixed(2) }}</td>
          <td class="numericColumn"
              v-if="loggedIn">{{ problem.score.toFixed(2) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style>
.tag, .tag-autogenerated {
  margin-right: .25em;
}

.omegaup-quality-badge {
  width: 18px;
  height: 18px;
  background: url('/media/quality-badge-sm.png') center/contain no-repeat;
}
</style>

<script>
import {T} from '../../omegaup.js';
import UI from '../../ui.js';

export default {
  props: {
    problems: Array,
    loggedIn: Boolean,
    currentTags: Array,
  },
  data: function() {
    return {
      T: T, UI: UI, qualityTags:
                        [
                          T.qualityFormQualityVeryBad,
                          T.qualityFormQualityBad,
                          T.qualityFormQualityFair,
                          T.qualityFormQualityGood,
                          T.qualityFormQualityVeryGood,
                        ],
          difficultyTags:[
            T.qualityFormDifficultyVeryEasy,
            T.qualityFormDifficultyEasy,
            T.qualityFormDifficultyMedium,
            T.qualityFormDifficultyHard,
            T.qualityFormDifficultyVeryHard
          ],
    }
  },
  methods: {
    rowClassForProblem: function(problemVisibility) {
      return problemVisibility >= 2 ? 'high-quality' : '';
    },
    iconClassForProblem: function(problemVisibility) {
      if (problemVisibility < 0)
        return 'glyphicon-ban-circle';
      else if (problemVisibility == 0)
        return 'glyphicon-eye-close';
      else if (problemVisibility >= 2)
        return 'omegaup-quality-badge';
    },
    iconTitleForProblem: function(problemVisibility) {
      if (problemVisibility < 0)
        return T.wordsBannedProblem;
      else if (problemVisibility == 0)
        return T.wordsPrivate;
      else if (problemVisibility >= 2)
        return T.wordsHighQualityProblem;
    },
    hrefForProblemTag: function(currentTags, problemTag) {
      if (!currentTags) return `/problem/?tag[]=${problemTag}`;
      let tags = currentTags.slice();
      if (!tags.includes(problemTag)) tags.push(problemTag);
      return `/problem/?tag[]=${tags.join('&tag[]=')}`;
    },
  }
}
</script>
